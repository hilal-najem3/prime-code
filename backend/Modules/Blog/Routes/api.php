<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Controllers\ArticleController;

/*
|--------------------------------------------------------------------------
| Blog Module API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /articles
| Protected by Sanctum + AccessMiddleware
|
*/

Route::prefix('articles')
    ->middleware(['tenant', 'auth:sanctum'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | View Articles
        |--------------------------------------------------------------------------
        */

        Route::get('/', [ArticleController::class, 'index'])
            ->name('articles.index')
            ->middleware('access:permission,articles.view');

        Route::get('/{article}', [ArticleController::class, 'show'])
            ->name('articles.show')
            ->middleware('access:permission,articles.view');

        /*
        |--------------------------------------------------------------------------
        | Create Article
        |--------------------------------------------------------------------------
        */

        Route::post('/', [ArticleController::class, 'store'])
            ->name('articles.store')
            ->middleware('access:permission,articles.create');

        /*
        |--------------------------------------------------------------------------
        | Update Article
        |--------------------------------------------------------------------------
        */

        Route::put('/{article}', [ArticleController::class, 'update'])
            ->name('articles.update')
            ->middleware('access:permission,articles.update');

        /*
        |--------------------------------------------------------------------------
        | Delete Article
        |--------------------------------------------------------------------------
        */

        Route::delete('/{article}', [ArticleController::class, 'destroy'])
            ->name('articles.delete')
            ->middleware('access:permission,articles.delete');
    });
