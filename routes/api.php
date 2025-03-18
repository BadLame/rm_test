<?php

use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    require 'auth.php';

    Route::middleware('auth:api')->group(function () {
        // todo
    });
});
