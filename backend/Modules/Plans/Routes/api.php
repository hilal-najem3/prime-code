<?php

use Illuminate\Support\Facades\Route;
use Modules\Plans\Controllers\PlansController;

Route::prefix('plans')
    ->middleware(['tenant', 'auth:sanctum'])
    ->group(function () {

        Route::get('/', [PlansController::class, 'index'])
            ->name('plans.index')
            ->middleware('access:permission,plans.index');

        Route::post('/', [PlansController::class, 'store'])
            ->name('plans.store')
            ->middleware('access:permission,plans.store');

        Route::put('/{plan}', [PlansController::class, 'update'])
            ->name('plans.update')
            ->middleware('access:permission,plans.update');

        Route::delete('/{plan}', [PlansController::class, 'destroy'])
            ->name('plans.destroy')
            ->middleware('access:permission,plans.destroy');
    });
