<?php

use Illuminate\Support\Facades\Route;
use Modules\Media\Controllers\MediaController;

Route::prefix('media')
    ->middleware(['auth:sanctum'])
    ->group(function () {

        Route::get('/', [MediaController::class, 'index'])
            ->name('media.index')
            ->middleware('access:auto');

        Route::post('/upload', [MediaController::class, 'upload'])
            ->name('media.upload')
            ->middleware('access:auto');

        Route::post('/attach', [MediaController::class, 'attach'])
            ->name('media.attach')
            ->middleware('access:auto');

        Route::delete('/', [MediaController::class, 'destroy'])
            ->name('media.delete')
            ->middleware('access:auto');
    });