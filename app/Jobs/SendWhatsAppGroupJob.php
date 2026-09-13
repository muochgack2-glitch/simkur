<?php

namespace App\Jobs;

use App\Models\WaLog;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppGroupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maksimal percobaan jika gagal.
     */
    public int $tries = 3;

    /**
     * Timeout tiap percobaan (detik).
     */
    public int $timeout = 30;

    /**
     * Backoff delay antar retry (detik).
     */
    public array $backoff = [10, 30, 60];

    public function __construct(
        public readonly string  $groupId,
        public readonly string  $message,
        public readonly ?string $gatewayUrl = null,
    ) {}

    public function handle(): void
    {
        $wa     = new WhatsAppService($this->gatewayUrl);
        $result = $wa->sendToGroup($this->groupId, $this->message);

        if (! ($result['success'] ?? false)) {
            $error = $result['message'] ?? 'Unknown error';
            Log::warning('[WA Group Job] Gagal kirim, akan retry', [
                'group'    => $this->groupId,
                'attempt'  => $this->attempts(),
                'error'    => $error,
            ]);
            // Lempar exception agar queue retry otomatis
            throw new \RuntimeException("WA sendToGroup gagal: {$error}");
        }

        Log::info('[WA Group Job] Berhasil', [
            'group'   => $this->groupId,
            'attempt' => $this->attempts(),
        ]);
    }

    /**
     * Dipanggil setelah semua retry habis (masuk failed_jobs).
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('[WA Group Job] GAGAL TOTAL setelah ' . $this->tries . ' percobaan', [
            'group'   => $this->groupId,
            'message' => $this->message,
            'error'   => $exception->getMessage(),
        ]);

        // Update log terakhir jadi failed jika ada
        WaLog::where('recipient', $this->groupId)
            ->where('status', 'error')
            ->latest()
            ->first()?->update(['status' => 'failed']);
    }
}
