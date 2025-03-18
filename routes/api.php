<?php

use App\Http\Middleware\Auth\EnsureUserNotBlockedMiddleware as UserNotBlocked;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    require 'auth.php';

    Route::middleware(['auth:api', UserNotBlocked::class])->group(function () {
        // todo
    });
});
