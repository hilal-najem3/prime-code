<?php

use Illuminate\Support\Facades\Route;
use Modules\Pages\Controllers\PagesController;

/*
|--------------------------------------------------------------------------
| Pages Module API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /pages
| and protected using AccessMiddleware.
|
*/

Route::prefix('pages')
    ->middleware(['tenant', 'auth:sanctum'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | View Pages
        |--------------------------------------------------------------------------
        */

        Route::get('/', [PagesController::class, 'index'])
            ->name('pages.index')
            ->middleware('access:permission,pages.view');

        Route::get('/{page}', [PagesController::class, 'show'])
            ->name('pages.show')
            ->middleware('access:permission,pages.view');

        /*
        |--------------------------------------------------------------------------
        | Create Page
        |--------------------------------------------------------------------------
        */

        Route::post('/', [PagesController::class, 'store'])
            ->name('pages.store')
            ->middleware('access:permission,pages.create');

        /*
        |--------------------------------------------------------------------------
        | Update Page
        |--------------------------------------------------------------------------
        */

        Route::put('/{page}', [PagesController::class, 'update'])
            ->name('pages.update')
            ->middleware('access:permission,pages.update');

        /*
        |--------------------------------------------------------------------------
        | Delete Page
        |--------------------------------------------------------------------------
        */

        Route::delete('/{page}', [PagesController::class, 'destroy'])
            ->name('pages.delete')
            ->middleware('access:permission,pages.delete');
    });

/*
|--------------------------------------------------------------------------
| Platform -> Manage Tenant Pages
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'access:auto', 'platform.tenant'])
    ->prefix('platform/tenants/{tenant}/pages')
    ->group(function () {

        Route::get('/', [PagesController::class, 'index'])
            ->name('platform.tenants.pages.index');

        Route::get('/{page}', [PagesController::class, 'show'])
            ->name('platform.tenants.pages.show');

        Route::post('/', [PagesController::class, 'store'])
            ->name('platform.tenants.pages.store');

        Route::put('/{page}', [PagesController::class, 'update'])
            ->name('platform.tenants.pages.update');

        Route::delete('/{page}', [PagesController::class, 'destroy'])
            ->name('platform.tenants.pages.delete');
    });
