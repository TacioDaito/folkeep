<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register any user services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Contracts\UserServiceInterface::class, \App\Services\UserService::class);
    }

    /**
     * Bootstrap any user services.
     */
    public function boot(): void
    {
        //
    }
}
