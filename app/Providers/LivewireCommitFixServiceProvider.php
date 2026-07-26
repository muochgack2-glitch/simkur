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
     * This service provider fixes the Livewire $commit method issue.
     * Livewire JavaScript sends $commit method calls, but PHP components
     * don't have this method, causing MethodNotFoundException.
     * 
     * We use Livewire's 'call' hook to intercept $commit calls and return early.
     */
    public function boot(): void
    {
        Livewire::listen('call', function ($component, $method, $params, $context, $returnEarly) {
            // If the method being called is $commit, return early with null
            // This prevents the MethodNotFoundException from being thrown
            if ($method === '$commit') {
                $returnEarly(null);
            }
        });
    }
}
