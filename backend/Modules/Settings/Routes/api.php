<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Controllers\SettingsController;

/*
|--------------------------------------------------------------------------
| Settings Module API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /settings
| and protected using AccessMiddleware.
|
*/

Route::prefix('settings')
    ->middleware(['auth:sanctum'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | View Settings
        |--------------------------------------------------------------------------
        */

        Route::get('/', [SettingsController::class, 'index'])
            ->name('settings.index')
            ->middleware('access:permission,settings.view');

        Route::get('/{key}', [SettingsController::class, 'show'])
            ->name('settings.show')
            ->middleware('access:permission,settings.view');

        /*
        |--------------------------------------------------------------------------
        | Create / Update Settings
        |--------------------------------------------------------------------------
        */

        Route::post('/', [SettingsController::class, 'store'])
            ->name('settings.store')
            ->middleware('access:permission,settings.update');

        Route::put('/{key}', [SettingsController::class, 'update'])
            ->name('settings.update')
            ->middleware('access:permission,settings.update');

        /*
        |--------------------------------------------------------------------------
        | Delete Settings
        |--------------------------------------------------------------------------
        */

        Route::delete('/{key}', [SettingsController::class, 'destroy'])
            ->name('settings.destroy')
            ->middleware('access:permission,settings.delete');

        /*
        |--------------------------------------------------------------------------
        | Public Settings (No Auth Required)
        |--------------------------------------------------------------------------
        */
    });

/*
|--------------------------------------------------------------------------
| Public Settings Route
|--------------------------------------------------------------------------
|
| Accessible without authentication.
|
*/

Route::get('settings/public', [SettingsController::class, 'public'])
    ->name('settings.public');