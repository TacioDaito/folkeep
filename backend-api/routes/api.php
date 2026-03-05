<?php

use App\Http\Controllers\MeController;
use Illuminate\Support\Facades\Route;

Route::middleware('keycloak.auth')->group(function () {
    Route::get('/me', [MeController::class, 'show']);

    // Add all other protected routes here
});
