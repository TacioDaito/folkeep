<?php

use App\Http\Controllers\MeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware('keycloak.auth')->group(function () {
    Route::get('/me', [MeController::class, 'show']);
    Route::get('/user', [UserController::class, 'getUser']);
});
