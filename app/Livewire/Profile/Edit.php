<?php

namespace App\Livewire\Profile;

use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Livewire\BaseComponent;

class Edit extends BaseComponent
{
    public string $name = '';
    public string $nip_nuptk = '';
    public string $email = '';
    public string $no_hp = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name      = $user->name ?? '';
        $this->nip_nuptk = $user->nip_nuptk ?? '';
        $this->email     = $user->email ?? '';
        $this->no_hp     = $user->no_hp ?? '';
    }

    public function updateProfile(): void
    {
        $user = auth()->user();

        $rules = [
            'name'      => 'required|string|max:100',
            'nip_nuptk' => 'nullable|string|max:30|unique:users,nip_nuptk,' . $user->id,
            'email'     => 'nullable|email|max:100|unique:users,email,' . $user->id,
            'no_hp'     => 'nullable|string|max:20',
        ];

        $messages = [
            'name.required'      => 'Nama wajib diisi.',
            'name.max'           => 'Nama maksimal 100 karakter.',
            'nip_nuptk.max'      => 'NIY/NIP maksimal 30 karakter.',
            'nip_nuptk.unique'   => 'NIY/NIP sudah digunakan user lain.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah digunakan user lain.',
            'no_hp.max'          => 'No HP maksimal 20 karakter.',
        ];

        $this->validate($rules, $messages);

        $user->update([
            'name'      => $this->name,
            'nip_nuptk' => $this->nip_nuptk ?: null,
            'email'     => $this->email ?: null,
            'no_hp'     => $this->no_hp ?: null,
        ]);

        ActivityLog::createLog(
            action: 'update_profile',
            description: 'User memperbarui profil'
        );

        session()->flash('success', 'Profil berhasil diperbarui!');
    }

    #[Layout('components.layouts.app')]
    #[Title('Edit Profil - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        return view('livewire.profile.edit');
    }
}
