<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware('keycloak.auth')->group(function () {
    Route::get('/user', [UserController::class, 'getUser']);
});
