<?php

use Illuminate\Support\Facades\Route;
use Modules\Media\Controllers\MediaController;

Route::prefix('media')
    ->middleware(['tenant', 'auth:sanctum'])
    ->group(function () {

        Route::get('/', [MediaController::class, 'index'])
            ->name('media.index')
            ->middleware('access:auto');

        Route::get('/{media}/secure', [MediaController::class, 'secure'])
            ->name('media.secure')
            ->middleware(['access:auto']);

        Route::get('/{media}/usage', [MediaController::class, 'usage'])
            ->name('media.usage')
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