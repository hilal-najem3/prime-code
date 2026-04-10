<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Controllers\UsersController;

Route::middleware([
    'tenant',
    'auth:sanctum',
    'access:auto'
])->group(function () {
    Route::apiResource('users', UsersController::class);
});
