<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscriptions\Controllers\SubscriptionsController;

Route::prefix('tenants/{tenant}/subscription')
    ->middleware(['tenant', 'auth:sanctum'])
    ->group(function () {

        Route::post('/', [SubscriptionsController::class, 'assign'])
            ->name('subscriptions.assign')
            ->middleware('access:permission,subscriptions.assign');
    });
