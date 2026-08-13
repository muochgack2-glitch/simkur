<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AbsensiApiService
{
    /**
     * Base URL of the Absensi system API.
     */
    protected string $baseUrl;

    /**
     * API key for authentication.
     */
    protected ?string $apiKey;

    /**
     * Request timeout in seconds.
     */
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.absensi.api_url', 'https://absensi.smkpgriblora.sch.id'), '/');
        $this->apiKey = config('services.absensi.api_key');
        $this->timeout = 3; // 3 seconds timeout
    }

    /**
     * Get attendance/scan data from Absensi system for given NIS array on a date.
     *
     * Returns a collection keyed by NIS with status and check_in_time.
     * On failure, returns empty collection (graceful fallback).
     *
     * @param array $nisArray Array of NIS strings
     * @param string $date Date in Y-m-d format
     * @return Collection [nis => ['status' => '...', 'check_in_time' => '...']]
     */
    public function getAttendanceByNis(array $nisArray, string $date): Collection
    {
        if (empty($nisArray)) {
            return collect();
        }

        try {
            $headers = [];
            if ($this->apiKey) {
                $headers['X-API-Key'] = $this->apiKey;
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders($headers)
                ->get("{$this->baseUrl}/api/ekaldik/attendance", [
                    'date' => $date,
                    'nis' => $nisArray,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['success']) && $data['success'] && isset($data['data'])) {
                    return collect($data['data']);
                }
            }

            Log::warning('Absensi API returned non-success response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return collect();

        } catch (Exception $e) {
            Log::warning('Absensi API connection failed (fallback to default)', [
                'error' => $e->getMessage(),
                'url' => $this->baseUrl,
            ]);

            return collect();
        }
    }

    /**
     * Map Absensi scan status to E-Kaldik attendance status.
     *
     * Absensi statuses: hadir, terlambat, alpha, izin
     * E-Kaldik statuses: hadir, sakit, izin, alpha
     *
     * @param string|null $absensiStatus Status from Absensi system
     * @return string E-Kaldik compatible status
     */
    public static function mapStatus(?string $absensiStatus): string
    {
        return match ($absensiStatus) {
            'hadir', 'terlambat' => 'hadir',  // scan QR = hadir di kelas
            'izin' => 'izin',
            'alpha' => 'alpha',
            default => 'alpha',  // unknown/null = belum scan = alpha
        };
    }

    /**
     * Check if the Absensi API is configured and reachable.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        try {
            $headers = [];
            if ($this->apiKey) {
                $headers['X-API-Key'] = $this->apiKey;
            }

            $response = Http::timeout(2)
                ->withHeaders($headers)
                ->get("{$this->baseUrl}/api/ekaldik/attendance", [
                    'date' => date('Y-m-d'),
                    'nis' => ['test'],
                ]);

            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }
}
