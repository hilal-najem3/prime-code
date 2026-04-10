<?php

use Illuminate\Support\Facades\Route;
use Modules\Modules\Controllers\ModulesController as ModuleController;

Route::middleware(['tenant', 'auth:sanctum', 'access:permission,modules.index'])
    ->prefix('modules')
    ->group(function () {

        Route::get('/', [ModuleController::class, 'index'])->name('modules.index');

        Route::post('/', [ModuleController::class, 'store'])->name('modules.store');

        Route::get('/{module}', [ModuleController::class, 'show'])->name('modules.show');

        Route::put('/{module}', [ModuleController::class, 'update'])->name('modules.update');

        Route::delete('/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');
    });
