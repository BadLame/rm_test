<?php

use App\Http\Controllers\UsersController;
use App\Http\Middleware\Auth\EnsureUserNotBlockedMiddleware as UserNotBlocked;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    require 'auth.php';

    Route::middleware(['auth:api', UserNotBlocked::class])->group(function () {
        Route::get('/users', [UsersController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UsersController::class, 'show'])
            ->where(['user' => '[0-9]+'])
            ->name('users.show');
    });
});
