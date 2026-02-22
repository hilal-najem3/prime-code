<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Main\PageController;

Route::middleware(['setlocale'])->group(function () {

    Route::get('/', [PageController::class, 'home'])->name('home');

    // Localized routes with prefix
    Route::prefix('{locale}')
        ->where(['locale' => implode('|', config('locales.locales'))])
        ->group(function () {
            Route::get('/', [PageController::class, 'home'])->name('localized.home');
        });
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';