<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any auth services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\JwksCache::class);
        $this->app->singleton(\App\Services\KeycloakTokenValidator::class);
    }

    /**
     * Bootstrap any auth services.
     */
    public function boot(): void
    {
        //
    }
}
