<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\Auth\EnsureUserNotBlockedMiddleware as UserNotBlocked;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::name('auth.')->group(function () {
        Route::middleware('guest')->post('/login', [AuthController::class, 'login'])->name('login');
        Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout'])->name('logout');
    });

    Route::middleware(['auth:api', UserNotBlocked::class])->name('users.')->group(function () {
        Route::get('/users', [UsersController::class, 'index'])->name('index');

        Route::middleware('can:view,user')
            ->get('/users/{user}', [UsersController::class, 'show'])
            ->where(['user' => '[0-9]+'])
            ->name('show');

        Route::middleware('can:create,' . User::class)
            ->post('/users', [UsersController::class, 'create'])
            ->name('create');

        Route::middleware('can:block,user')
            ->post('/users/{user}/toggle-block', [UsersController::class, 'block'])
            ->where(['user' => '[0-9]+'])
            ->name('toggle-block');

        Route::middleware('can:update,user')->group(function () {
            Route::post('/users/{user}', [UsersController::class, 'update'])
                ->where(['user' => '[0-9]+'])
                ->name('update');

            Route::post('/users/{user}/change-password', [UsersController::class, 'changePassword'])
                ->where(['user' => '[0-9]+'])
                ->name('change-password');
        });
    });
});
