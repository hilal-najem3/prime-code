<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenants\Controllers\TenantsController as C;

Route::middleware(['auth:sanctum', 'access:auto'])
    ->prefix('platform')
    ->group(function () {

        Route::get('tenants', [C::class, 'index'])
            ->name('tenants.index');

        Route::post('tenants', [C::class, 'store'])
            ->name('tenants.store');

        Route::get('tenants/{tenant}', [C::class, 'show'])
            ->name('tenants.show');

        Route::put('tenants/{tenant}', [C::class, 'update'])
            ->name('tenants.update');

        Route::delete('tenants/{tenant}', [C::class, 'destroy'])
            ->name('tenants.destroy');
    });