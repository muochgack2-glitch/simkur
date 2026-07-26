<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class LivewireCommitFixServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     * 
     * This service provider fixes the Livewire internal method issues.
     * Livewire JavaScript sends $commit and $set method calls, but PHP components
     * don't have these methods, causing MethodNotFoundException.
     * 
     * We use Livewire's 'call' hook to intercept these calls and return early.
     */
    public function boot(): void
    {
        Livewire::listen('call', function ($component, $method, $params, $context, $returnEarly) {
            // If the method being called is $commit or $set, return early with null
            // This prevents the MethodNotFoundException from being thrown
            if ($method === '$commit' || $method === '$set') {
                $returnEarly(null);
            }
        });
    }
}
