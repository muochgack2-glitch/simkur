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

    public array $status = [];
    public string $statusLabel = 'Mengecek...';
    public string $statusColor = 'gray';

    public array $groups = [];
    public string $pklGroupId = '';
    public string $pklTemplate = '';

    public function mount()
    {
        $this->refreshStatus();
        $this->pklGroupId = Setting::getValue('wa_pkl_group_id', '');
        $this->pklTemplate = Setting::getValue('wa_pkl_template', '');
    }

    public function refreshStatus()
    {
        $wa = new WhatsAppService();
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

        // Load groups if connected
        if ($state === 'connected') {
            $this->groups = (new WhatsAppService())->getGroups();
        }
    }

    public function saveSettings()
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