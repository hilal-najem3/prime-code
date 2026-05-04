<?php

use Illuminate\Support\Facades\Route;
use Modules\Patients\Controllers\PatientsController;

/*
|--------------------------------------------------------------------------
| Patients API Routes
|--------------------------------------------------------------------------
|
| All routes are protected by:
| - auth:sanctum
| - tenant context
|
| Permissions are enforced via AccessMiddleware.
|
*/

Route::middleware(['tenant', 'auth:sanctum', 'access:auto'])
    ->prefix('patients')
    ->name('patients.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | CRUD Routes
        |--------------------------------------------------------------------------
        */

        Route::get('/', [PatientsController::class, 'index'])
            ->name('index');

        Route::post('/', [PatientsController::class, 'store'])
            ->name('store');

        Route::get('/{patient}', [PatientsController::class, 'show'])
            ->name('show');

        Route::put('/{patient}', [PatientsController::class, 'update'])
            ->name('update');

        Route::delete('/{patient}', [PatientsController::class, 'destroy'])
            ->name('destroy');
    });