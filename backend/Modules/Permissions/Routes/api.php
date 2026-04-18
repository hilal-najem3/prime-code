<?php

use Illuminate\Support\Facades\Route;
use Modules\Permissions\Controllers\RolesController;
use Modules\Permissions\Controllers\PermissionsController;

/*
|--------------------------------------------------------------------------
| SHARED CONFIG
|--------------------------------------------------------------------------
*/

$tenantAuth = ['tenant', 'auth:sanctum', 'access:auto'];
$platformTenantAuth = ['auth:sanctum', 'access:auto', 'platform.tenant'];

/*
|--------------------------------------------------------------------------
| PERMISSIONS (PLATFORM)
|--------------------------------------------------------------------------
*/

Route::middleware($tenantAuth)
    ->prefix('platform/permissions')
    ->controller(PermissionsController::class)
    ->group(function () {
        Route::get('/', 'index')->name('platform.permissions.index');
    });

/*
|--------------------------------------------------------------------------
| REUSABLE ROLES ROUTES
|--------------------------------------------------------------------------
*/

$rolesRoutes = function (string $namePrefix = '') {
    Route::get('/', 'index')->name($namePrefix . 'roles.index');
    Route::post('/', 'store')->name($namePrefix . 'roles.store');
    Route::get('{role}', 'show')->name($namePrefix . 'roles.show');
    Route::put('{role}', 'update')->name($namePrefix . 'roles.update');
    Route::delete('{role}', 'destroy')->name($namePrefix . 'roles.destroy');
};

/*
|--------------------------------------------------------------------------
| PLATFORM ROLES
|--------------------------------------------------------------------------
*/

Route::middleware($tenantAuth)
    ->prefix('platform/roles')
    ->controller(RolesController::class)
    ->group(fn() => $rolesRoutes('platform.'));

/*
|--------------------------------------------------------------------------
| TENANT ROLES
|--------------------------------------------------------------------------
*/

Route::middleware($tenantAuth)
    ->prefix('tenant/roles')
    ->controller(RolesController::class)
    ->group(fn() => $rolesRoutes('tenant.'));

/*
|--------------------------------------------------------------------------
| PLATFORM → TENANT ROLES
|--------------------------------------------------------------------------
*/

Route::middleware($platformTenantAuth)
    ->prefix('platform/tenant/{tenant_id}/roles')
    ->controller(RolesController::class)
    ->group(fn() => $rolesRoutes('platform.tenant.'));
