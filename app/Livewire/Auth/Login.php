<?php

namespace App\Livewire\Auth;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Login extends Component
{
    public string $username = '';
    public string $password = '';
    public bool $remember = false;

    protected $rules = [
        'username' => 'required|string',
        'password' => 'required|string',
    ];

    protected $messages = [
        'username.required' => 'Username wajib diisi.',
        'password.required' => 'Password wajib diisi.',
    ];

    public function mount()
    {
        // Redirect if already authenticated
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
    }

    public function login()
    {
        $this->validate();

        // Rate limiting (max 5 attempts per minute)
        $key = 'login:' . request()->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            $this->addError('username', "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.");
            return;
        }

        // Attempt login with username
        $credentials = [
            'username' => $this->username,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            // Check if user is active
            if (!Auth::user()->is_active) {
                Auth::logout();
                RateLimiter::hit($key, 60);
                
                $this->addError('username', 'Akun Anda tidak aktif. Hubungi administrator.');
                return;
            }

            // Clear rate limiter on successful login
            RateLimiter::clear($key);

            // Update last login
            Auth::user()->update([
                'last_login_at' => now(),
            ]);

            // Log login activity
            ActivityLog::createLog(
                action: 'login',
                description: 'User berhasil login ke sistem'
            );

            request()->session()->regenerate();

            // Redirect based on role
            return redirect()->intended(route('dashboard'));
        }

        // Increment rate limiter on failed attempt
        RateLimiter::hit($key, 60);

        $this->addError('username', 'Username atau password salah.');
    }

    #[Layout('components.layouts.guest')]
    #[Title('Login - e-KALDIK')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
