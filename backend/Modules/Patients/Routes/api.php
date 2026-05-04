<?php

use Illuminate\Support\Facades\Route;
use Modules\Patients\Controllers\PatientController;

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

        Route::get('/', [PatientController::class, 'index'])
            ->name('index');

        Route::post('/', [PatientController::class, 'store'])
            ->name('store');

        Route::get('/{patient}', [PatientController::class, 'show'])
            ->name('show');

        Route::put('/{patient}', [PatientController::class, 'update'])
            ->name('update');

        Route::delete('/{patient}', [PatientController::class, 'destroy'])
            ->name('destroy');
    });