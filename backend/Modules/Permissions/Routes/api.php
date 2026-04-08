<?php

use Illuminate\Support\Facades\Route;
use Modules\Permissions\Controllers\RolesController;

Route::middleware(['auth:sanctum', 'access:auto'])
    ->prefix('platform/roles')
    ->group(function () {

        Route::get('/', [RolesController::class, 'index'])
            ->name('platform.roles.index');

        Route::post('/', [RolesController::class, 'store'])
            ->name('platform.roles.store');

        Route::get('{role}', [RolesController::class, 'show'])
            ->name('platform.roles.show');

        Route::put('{role}', [RolesController::class, 'update'])
            ->name('platform.roles.update');

        Route::delete('{role}', [RolesController::class, 'destroy'])
            ->name('platform.roles.destroy');
    });
