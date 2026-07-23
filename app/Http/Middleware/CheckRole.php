<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user is active
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
        }

        // If no roles specified, just check if authenticated
        if (empty($roles)) {
            return $next($request);
        }

        // Ensure user role is not null
        if (!$user->role) {
            \Log::error('User role is null', [
                'user_id' => $user->id,
                'username' => $user->username,
            ]);
            abort(403, 'Role pengguna tidak ditemukan. Hubungi administrator.');
        }

        // Check if user has one of the required roles
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // User doesn't have required role
        \Log::warning('Access denied - role mismatch', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'required_roles' => $roles,
            'url' => $request->url(),
        ]);
        
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
