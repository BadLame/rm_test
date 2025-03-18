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

        Route::post('/users', [UsersController::class, 'create'])->name('users.create');
        Route::post('/users/{user}', [UsersController::class, 'update'])
            ->where(['user' => '[0-9]+'])
            ->name('users.update');
        Route::post('/users/{user}/toggle-block', [UsersController::class, 'block'])
            ->where(['user' => '[0-9]+'])
            ->name('users.toggle-block');
        Route::post('/users/{user}/change-password', [UsersController::class, 'changePassword'])
            ->where(['user' => '[0-9]+'])
            ->name('users.change-password');
    });
});
