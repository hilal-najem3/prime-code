<?php

use Illuminate\Support\Facades\Route;
use Modules\Languages\Controllers\LanguagesController;

/*
|--------------------------------------------------------------------------
| Tenant Languages (Resolved via TenantResolver)
|--------------------------------------------------------------------------
*/

Route::middleware(['tenant', 'auth:sanctum', 'access:auto'])
    ->prefix('languages')
    ->group(function () {

        Route::get('/', [LanguagesController::class, 'index'])
            ->name('languages.index');

        Route::post('/', [LanguagesController::class, 'store'])
            ->name('languages.store');

        Route::get('{language}', [LanguagesController::class, 'show'])
            ->name('languages.show');

        Route::put('{language}', [LanguagesController::class, 'update'])
            ->name('languages.update');

        Route::delete('{language}', [LanguagesController::class, 'destroy'])
            ->name('languages.destroy');

        Route::patch('{language}/toggle', [LanguagesController::class, 'toggleActive'])
            ->name('languages.toggle');

        Route::patch('{language}/default', [LanguagesController::class, 'setDefault'])
            ->name('languages.default');
    });

/*
|--------------------------------------------------------------------------
| Platform → Manage Tenant Languages
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'access:auto', 'platform.tenant'])
    ->prefix('platform/tenants/{tenant}/languages')
    ->group(function () {

        Route::get('/', [LanguagesController::class, 'index'])
            ->name('platform.tenants.languages.index');

        Route::post('/', [LanguagesController::class, 'store'])
            ->name('platform.tenants.languages.store');

        Route::put('{language}', [LanguagesController::class, 'update'])
            ->name('platform.tenants.languages.update');

        Route::delete('{language}', [LanguagesController::class, 'destroy'])
            ->name('platform.tenants.languages.destroy');

        Route::patch('{language}/toggle', [LanguagesController::class, 'toggleActive'])
            ->name('platform.tenants.languages.toggle');

        Route::patch('{language}/default', [LanguagesController::class, 'setDefault'])
            ->name('platform.tenants.languages.default');
    });
