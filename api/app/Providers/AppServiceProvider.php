<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\JwksCache::class);
        $this->app->singleton(\App\Services\KeycloakTokenValidator::class);
        $this->app->bind(\App\Contracts\UserContract::class, \App\Services\UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
