<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\WaLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $baseUrl;
    private string $apiKey;
    private string $gatewayName;

    // Gateway yang tersedia — bisa dikembangkan dari config
    public static array $gateways = [
        'http://localhost:3000' => 'Port 3000 (wa-spmb)',
        'http://localhost:3001' => 'Port 3001 (wa-absensi)',
    ];

    public function __construct(?string $gatewayUrl = null)
    {
        // Prioritas: parameter → Setting DB → .env → default
        $this->baseUrl = rtrim(
            $gatewayUrl
                ?? Setting::getValue('wa_active_gateway')
                ?? config('services.whatsapp.url', 'http://localhost:3000'),
            '/'
        );
        $this->apiKey      = config('services.whatsapp.api_key', '');
        $this->gatewayName = self::$gateways[$this->baseUrl] ?? $this->baseUrl;
    }

    /**
     * URL gateway yang sedang aktif.
     */
    public function getActiveUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Nama gateway yang sedang aktif.
     */
    public function getActiveName(): string
    {
        return $this->gatewayName;
    }

    /**
     * Get gateway connection status.
     */
    public function getStatus(): array
    {
        try {
            $response = Http::withHeaders(['x-api-key' => $this->apiKey])
                ->timeout(5)
                ->get("{$this->baseUrl}/status");

            $data = $response->json() ?? ['status' => 'unknown'];
            $data['gateway_url']  = $this->baseUrl;
            $data['gateway_name'] = $this->gatewayName;
            return $data;
        } catch (\Exception $e) {
            return [
                'status'       => 'unreachable',
                'error'        => $e->getMessage(),
                'gateway_url'  => $this->baseUrl,
                'gateway_name' => $this->gatewayName,
            ];
        }
    }

    /**
     * Get list of WA groups.
     */
    public function getGroups(): array
    {
        try {
            $response = Http::withHeaders(['x-api-key' => $this->apiKey])
                ->timeout(10)
                ->get("{$this->baseUrl}/groups");

            $data = $response->json();
            return $data['success'] ? ($data['groups'] ?? []) : [];
        } catch (\Exception $e) {
            Log::error('WA getGroups failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Send message to a WA group.
     */
    public function sendToGroup(string $groupId, string $message): array
    {
        try {
            $response = Http::withHeaders(['x-api-key' => $this->apiKey])
                ->timeout(15)
                ->post("{$this->baseUrl}/send-group", [
                    'groupId' => $groupId,
                    'message' => $message,
                ]);

            $result = $response->json() ?? [];

            WaLog::create([
                'type'      => 'group',
                'recipient' => $groupId,
                'message'   => $message,
                'response'  => json_encode($result),
                'status'    => ($result['success'] ?? false) ? 'sent' : 'failed',
            ]);

            return $result;
        } catch (\Exception $e) {
            WaLog::create([
                'type'      => 'group',
                'recipient' => $groupId,
                'message'   => $message,
                'response'  => $e->getMessage(),
                'status'    => 'error',
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send message to a phone number.
     */
    public function send(string $phone, string $message): array
    {
        try {
            $response = Http::withHeaders(['x-api-key' => $this->apiKey])
                ->timeout(15)
                ->post("{$this->baseUrl}/send", [
                    'phone'   => $phone,
                    'message' => $message,
                ]);

            $result = $response->json() ?? [];

            WaLog::create([
                'type'      => 'personal',
                'recipient' => $phone,
                'message'   => $message,
                'response'  => json_encode($result),
                'status'    => ($result['success'] ?? false) ? 'sent' : 'failed',
            ]);

            return $result;
        } catch (\Exception $e) {
            WaLog::create([
                'type'      => 'personal',
                'recipient' => $phone,
                'message'   => $message,
                'response'  => $e->getMessage(),
                'status'    => 'error',
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
