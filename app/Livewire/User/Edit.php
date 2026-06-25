<?php

namespace App\Livewire\User;

use App\Models\ActivityLog;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Edit extends Component
{
    public $userId;
    public $name = '';
    public $username = ''; // read-only, not editable
    public $email = '';
    public $role = '';
    public $is_active = true;

    public function mount($id)
    {
        $user = User::findOrFail($id);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->is_active = $user->is_active;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->userId,
            'role' => 'required|in:admin,waka_kurikulum,kepala_sekolah,guru',
            'is_active' => 'boolean',
        ];
    }

    protected $messages = [
        'name.required' => 'Nama lengkap wajib diisi.',
        'name.max' => 'Nama maksimal 255 karakter.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan.',
        'email.max' => 'Email maksimal 255 karakter.',
        'role.required' => 'Role wajib dipilih.',
        'role.in' => 'Role tidak valid.',
    ];

    public function save()
    {
        if (!auth()->user()->canManageUsers()) {
            $this->addError('error', 'Anda tidak memiliki akses untuk mengedit user.');
            return;
        }

        // Prevent editing own role
        if ($this->userId === auth()->id() && $this->role !== auth()->user()->role) {
            $this->addError('role', 'Anda tidak dapat mengubah role Anda sendiri.');
            return;
        }

        // Kepala Sekolah tidak bisa mengubah role ke admin atau kepala sekolah
        if (auth()->user()->isKepalaSekolah() && in_array($this->role, ['admin', 'kepala_sekolah'])) {
            $this->addError('role', 'Anda tidak dapat mengubah role user menjadi Admin atau Kepala Sekolah.');
            return;
        }

        $this->validate();

        $user = User::findOrFail($this->userId);
        
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->is_active,
        ]);

        ActivityLog::createLog(
            action: 'update',
            modelType: 'User',
            modelId: $user->id,
            description: "Mengubah data user: {$user->name}"
        );

        session()->flash('success', 'User berhasil diperbarui!');

        return redirect()->route('users.index');
    }

    #[Layout('components.layouts.app')]
    #[Title('Edit Pengguna - e-KALDIK')]
    public function render()
    {
        return view('livewire.user.edit');
    }
}
