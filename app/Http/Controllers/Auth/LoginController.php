<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        // Redirect if already authenticated
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Rate limiting (max 5 attempts per minute)
        $key = 'login:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            return back()->withErrors([
                'username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik."
            ])->withInput($request->only('username'));
        }

        // Attempt login
        $credentials = $request->only('username', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Check if user is active
            if (!Auth::user()->is_active) {
                Auth::logout();
                RateLimiter::hit($key, 60);
                
                return back()->withErrors([
                    'username' => 'Akun Anda tidak aktif. Hubungi administrator.'
                ])->withInput($request->only('username'));
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

            // Regenerate session
            $request->session()->regenerate();

            // Redirect to dashboard
            return redirect()->intended(route('dashboard'));
        }

        // Increment rate limiter on failed attempt
        RateLimiter::hit($key, 60);

        return back()->withErrors([
            'username' => 'Username atau password salah.'
        ])->withInput($request->only('username'));
    }
}
