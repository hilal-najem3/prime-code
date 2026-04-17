<?php

use Illuminate\Support\Facades\Route;
use Modules\Pages\Controllers\PageResolverController;

/*
|--------------------------------------------------------------------------
| Public Website Page Routes
|--------------------------------------------------------------------------
|
| Catch-all route for CMS pages.
|
| IMPORTANT:
| - Must be the LAST registered route in the system
| - Handles all public page rendering
|
*/

Route::middleware(['tenant'])
    ->group(function () {

        Route::get('/{slug?}', [PageResolverController::class, 'resolve'])
            ->where('slug', '.*')
            ->name('pages.resolve');
    });