<?php

namespace App\Livewire\Auth;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ChangePassword extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected $rules = [
        'current_password' => 'required|string',
        'password' => 'required|string|min:8|confirmed',
    ];

    protected $messages = [
        'current_password.required' => 'Password saat ini wajib diisi.',
        'password.required' => 'Password baru wajib diisi.',
        'password.min' => 'Password minimal 8 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ];

    public function changePassword()
    {
        $this->validate();

        $user = auth()->user();

        // Verify current password
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini salah.');
            return;
        }

        // Check if new password is same as current
        if (Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Password baru tidak boleh sama dengan password saat ini.');
            return;
        }

        // Update password
        $user->update([
            'password' => Hash::make($this->password),
        ]);

        // Log activity
        ActivityLog::createLog(
            action: 'change_password',
            description: 'User mengubah password'
        );

        // Reset form
        $this->reset(['current_password', 'password', 'password_confirmation']);

        // Flash success message
        session()->flash('success', 'Password berhasil diubah!');

        // Optionally logout and redirect to login
        // Auth::logout();
        // return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login dengan password baru.');
    }

    #[Layout('components.layouts.app')]
    #[Title('Ganti Password - e-KALDIK')]
    public function render()
    {
        return view('livewire.auth.change-password');
    }
}
