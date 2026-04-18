<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Controllers\AuthenticationController;
use Modules\Auth\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('auth')
    ->middleware('tenant')
    ->controller(AuthenticationController::class)
    ->group(function () {

        Route::post('login', 'authenticate')->name('auth.login');
        Route::post('refresh', 'refresh')->name('auth.refresh');

        Route::post('logout', 'logout')
            ->middleware('auth:sanctum')
            ->name('auth.logout');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', 'me');
            Route::get('profile', 'profile');
            Route::put('profile', 'updateProfile');
            Route::put('password', 'updatePassword');
        });
    });

/*
|--------------------------------------------------------------------------
| REUSABLE USERS ROUTES
|--------------------------------------------------------------------------
*/

$usersRoutes = function (string $namePrefix = '') {
    Route::get('/', 'index')->name($namePrefix . 'users.index');
    Route::post('/', 'store')->name($namePrefix . 'users.store');
    Route::get('{user}', 'show')->name($namePrefix . 'users.show');
    Route::put('{user}', 'update')->name($namePrefix . 'users.update');
    Route::delete('{user}', 'destroy')->name($namePrefix . 'users.destroy');
};

/*
|--------------------------------------------------------------------------
| PLATFORM USERS
|--------------------------------------------------------------------------
*/

Route::middleware(['tenant', 'auth:sanctum', 'access:auto'])
    ->prefix('platform/users')
    ->controller(UsersController::class)
    ->group(fn() => $usersRoutes('platform.'));

/*
|--------------------------------------------------------------------------
| TENANT USERS
|--------------------------------------------------------------------------
*/

Route::middleware(['tenant', 'auth:sanctum', 'access:auto'])
    ->prefix('tenant/users')
    ->controller(UsersController::class)
    ->group(fn() => $usersRoutes('tenant.'));

/*
|--------------------------------------------------------------------------
| PLATFORM → TENANT USERS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'access:auto', 'platform.tenant'])
    ->prefix('platform/tenant/{tenant_id}/users')
    ->controller(UsersController::class)
    ->group(fn() => $usersRoutes('platform.tenant.'));
