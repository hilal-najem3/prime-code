<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Controllers\AuthenticationController;
use Modules\Auth\Controllers\UsersController;

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

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthenticationController::class, 'me']);
        });
    });

Route::middleware(['auth:sanctum', 'access:auto'])
    ->prefix('platform/users')
    ->group(function () {

        Route::get('/', [UsersController::class, 'index'])
            ->name('platform.users.index');

        Route::post('/', [UsersController::class, 'store'])
            ->name('platform.users.store');

        Route::get('{user}', [UsersController::class, 'show'])
            ->name('platform.users.show');

        Route::put('{user}', [UsersController::class, 'update'])
            ->name('platform.users.update');

        Route::delete('{user}', [UsersController::class, 'destroy'])
            ->name('platform.users.destroy');
    });
