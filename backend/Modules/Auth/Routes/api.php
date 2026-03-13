<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Controllers\AuthenticationController;

Route::prefix('auth')
    ->group(function () {

        Route::post(
            'login',
            [AuthenticationController::class, 'authenticate']
        )->name('auth.login');

        Route::post(
            'refresh',
            [AuthenticationController::class, 'refresh']
        )->name('auth.refresh');

        Route::post(
            'logout',
            [AuthenticationController::class, 'logout']
        )
            ->middleware('auth:sanctum')
            ->name('auth.logout');
    });