<?php

namespace App\Livewire\User;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    public $name = '';
    public $username = '';
    public $email = '';
    public $password = 'password'; // default password
    public $role = 'guru';
    public $is_active = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username|alpha_dash',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,waka_kurikulum,guru',
            'is_active' => 'boolean',
        ];
    }

    protected $messages = [
        'name.required' => 'Nama lengkap wajib diisi.',
        'name.max' => 'Nama maksimal 255 karakter.',
        'username.required' => 'Username wajib diisi.',
        'username.unique' => 'Username sudah digunakan.',
        'username.alpha_dash' => 'Username hanya boleh huruf, angka, dash, dan underscore.',
        'username.max' => 'Username maksimal 255 karakter.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan.',
        'email.max' => 'Email maksimal 255 karakter.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 6 karakter.',
        'role.required' => 'Role wajib dipilih.',
        'role.in' => 'Role tidak valid.',
    ];

    public function generateUsername()
    {
        if (!empty($this->name)) {
            // Generate username from name (lowercase, remove spaces, remove special chars)
            $username = strtolower(str_replace(' ', '', $this->name));
            $username = preg_replace('/[^a-z0-9_]/', '', $username);
            
            // Truncate if too long
            if (strlen($username) > 20) {
                $username = substr($username, 0, 20);
            }
            
            $this->username = $username;
        }
    }

    public function save()
    {
        if (!auth()->user()->isAdmin()) {
            $this->addError('error', 'Anda tidak memiliki akses untuk menambah user.');
            return;
        }

        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'is_active' => $this->is_active,
        ]);

        ActivityLog::createLog(
            action: 'create',
            modelType: 'User',
            modelId: $user->id,
            description: "Membuat user baru: {$user->name} ({$user->role})"
        );

        session()->flash('success', 'User berhasil ditambahkan!');

        return redirect()->route('users.index');
    }

    #[Layout('components.layouts.app')]
    #[Title('Tambah Pengguna - e-KALDIK')]
    public function render()
    {
        return view('livewire.user.create');
    }
}
