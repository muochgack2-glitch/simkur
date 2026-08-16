<?php

namespace App\Services;

use App\Models\WaLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.whatsapp.url', 'http://localhost:3000'), '/');
        $this->apiKey = config('services.whatsapp.api_key', '');
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

            return $response->json() ?? ['status' => 'unknown'];
        } catch (\Exception $e) {
            return ['status' => 'unreachable', 'error' => $e->getMessage()];
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

            // Log
            WaLog::create([
                'type' => 'group',
                'recipient' => $groupId,
                'message' => $message,
                'response' => json_encode($result),
                'status' => ($result['success'] ?? false) ? 'sent' : 'failed',
            ]);

            return $result;
        } catch (\Exception $e) {
            WaLog::create([
                'type' => 'group',
                'recipient' => $groupId,
                'message' => $message,
                'response' => $e->getMessage(),
                'status' => 'error',
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
                    'phone' => $phone,
                    'message' => $message,
                ]);

            $result = $response->json() ?? [];

            WaLog::create([
                'type' => 'personal',
                'recipient' => $phone,
                'message' => $message,
                'response' => json_encode($result),
                'status' => ($result['success'] ?? false) ? 'sent' : 'failed',
            ]);

            return $result;
        } catch (\Exception $e) {
            WaLog::create([
                'type' => 'personal',
                'recipient' => $phone,
                'message' => $message,
                'response' => $e->getMessage(),
                'status' => 'error',
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}