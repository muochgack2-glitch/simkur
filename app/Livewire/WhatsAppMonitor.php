<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Models\WaLog;
use App\Services\WhatsAppService;
use Livewire\Component;
use Livewire\WithPagination;

class WhatsAppMonitor extends Component
{
    use WithPagination;

    public array  $status      = [];
    public string $statusLabel = 'Mengecek...';
    public string $statusColor = 'gray';

    public array  $groups      = [];
    public string $pklGroupId  = '';
    public string $pklTemplate = '';

    // Gateway selector
    public string $activeGateway = '';
    public array  $availableGateways = [];

    public function mount()
    {
        $this->availableGateways = WhatsAppService::$gateways;

        // Baca gateway aktif dari settings, fallback ke config
        $this->activeGateway = Setting::getValue('wa_active_gateway')
            ?? config('services.whatsapp.url', 'http://localhost:3000');

        $this->refreshStatus();

        $this->pklGroupId  = Setting::getValue('wa_pkl_group_id', '');
        $this->pklTemplate = Setting::getValue('wa_pkl_template', '');
    }

    /**
     * Dipanggil otomatis saat dropdown gateway berubah (wire:model).
     */
    public function updatedActiveGateway(string $value): void
    {
        // Simpan ke settings DB
        Setting::setValue('wa_active_gateway', $value, 'string', 'whatsapp');

        // Langsung cek status gateway yang baru dipilih
        $this->refreshStatus();
    }

    public function refreshStatus(): void
    {
        $wa = new WhatsAppService($this->activeGateway ?: null);
        $this->status = $wa->getStatus();

        $state = $this->status['status'] ?? 'unknown';

        $this->statusLabel = match ($state) {
            'connected'    => 'Terhubung',
            'disconnected' => 'Terputus',
            'qr'           => 'Menunggu Scan QR',
            'unreachable'  => 'Server Tidak Aktif',
            default        => 'Tidak Diketahui',
        };

        $this->statusColor = match ($state) {
            'connected'    => 'green',
            'disconnected' => 'red',
            'qr'           => 'yellow',
            default        => 'gray',
        };

        // Muat grup jika terhubung
        if ($state === 'connected') {
            $this->groups = $wa->getGroups();
        } else {
            $this->groups = [];
        }
    }

    public function saveSettings(): void
    {
        Setting::setValue('wa_pkl_group_id', $this->pklGroupId, 'string', 'whatsapp');
        Setting::setValue('wa_pkl_template', $this->pklTemplate, 'string', 'whatsapp');
        session()->flash('success', 'Pengaturan berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.whatsapp-monitor', [
            'logs' => WaLog::latest()->paginate(20),
        ])->layout('components.layouts.app', ['title' => 'WhatsApp Gateway']);
    }
}
