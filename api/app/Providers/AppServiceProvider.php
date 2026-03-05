<?php

namespace App\Providers;

use App\Services\JwksCache;
use App\Services\KeycloakTokenValidator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(JwksCache::class);
        $this->app->singleton(KeycloakTokenValidator::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
