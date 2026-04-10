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
