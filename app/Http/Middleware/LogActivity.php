<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users
        if (auth()->check()) {
            // Log page access for important routes
            $route = $request->route();
            
            if ($route && $this->shouldLog($request)) {
                ActivityLog::createLog(
                    action: 'page_access',
                    description: "Mengakses: {$request->path()}"
                );
            }
        }

        return $response;
    }

    /**
     * Determine if the request should be logged
     */
    private function shouldLog(Request $request): bool
    {
        // Don't log these routes
        $ignoredPaths = [
            'livewire/update',
            'livewire/message',
            '_debugbar',
        ];

        foreach ($ignoredPaths as $path) {
            if (str_contains($request->path(), $path)) {
                return false;
            }
        }

        // Only log GET requests to avoid duplicate logs
        return $request->isMethod('GET');
    }
}
