<?php

use Illuminate\Support\Facades\Route;
use Modules\Permissions\Controllers\RolesController;
use Modules\Permissions\Controllers\PermissionsController;

Route::middleware(['tenant', 'auth:sanctum', 'access:auto'])
    ->prefix('platform/permissions')
    ->group(
        function () {

            Route::get('/', [PermissionsController::class, 'index'])
                ->name('platform.permissions.index');
        }
    );

Route::middleware(['tenant', 'auth:sanctum', 'access:auto'])
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

Route::middleware(['auth:sanctum', 'access:auto', 'platform.tenant'])
    ->prefix('platform/tenant/{tenant_id}/roles')
    ->group(function () {

        Route::get('/', [RolesController::class, 'index'])
            ->name('platform.tenant.roles.index');

        Route::post('/', [RolesController::class, 'store'])
            ->name('platform.tenant.roles.store');

        Route::get('{role}', [RolesController::class, 'show'])
            ->name('platform.tenant.roles.show');

        Route::put('{role}', [RolesController::class, 'update'])
            ->name('platform.tenant.roles.update');

        Route::delete('{role}', [RolesController::class, 'destroy'])
            ->name('platform.tenant.roles.destroy');
    });
