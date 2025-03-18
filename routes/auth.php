<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::name('auth.')->group(function () {
    Route::middleware('guest')->post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout'])->name('logout');
});
