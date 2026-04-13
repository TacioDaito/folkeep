<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware('keycloak.auth')->group(function () {
    Route::get('/hello', fn() => response()->json(['message' => 'Hello!']));
    Route::get('/user', [UserController::class, 'getUser'])->name('user.info');
});
