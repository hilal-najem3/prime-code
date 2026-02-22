<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\LanguagesController;
use App\Http\Controllers\Admin\Projects\TagsController as ProjectsTagsController;
use App\Http\Controllers\Admin\Projects\CategoriesController as ProjectsCategoriesController;
use App\Http\Controllers\Admin\Projects\ProjectsController;
use App\Http\Controllers\Admin\Posts\TagsController;
use App\Http\Controllers\Admin\Posts\CategoriesController;
use App\Http\Controllers\Admin\Posts\PostsController;
use App\Http\Controllers\Admin\Services\ServiceOrdersController;
use App\Http\Controllers\Admin\Services\ServicesController;
use App\Http\Controllers\Admin\Products\TagsController as ProductsTagsController;
use App\Http\Controllers\Admin\Products\CategoriesController as ProductsCategoriesController;
use App\Http\Controllers\Admin\Products\ProductsController;
use App\Http\Controllers\Admin\Products\AttributesController;

Route::middleware(['auth', 'admin'])->group(function () {
    // Redirect /admin to /admin/dashboard
    Route::redirect('/admin', '/admin/dashboard');

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'dashboard'])
        ->name('admin.dashboard');

    Route::resource('/admin/users', UsersController::class)
        ->except(['show'])
        ->names('admin.users');

    Route::resource('/admin/languages', LanguagesController::class)
        ->except(['show'])
        ->names('admin.languages');

    Route::resource('/admin/post-tags', TagsController::class)
        ->except(['show'])
        ->names('admin.post-tags');

    Route::resource('/admin/post-categories', CategoriesController::class)
        ->except(['show'])
        ->names('admin.post-categories');

    Route::resource('/admin/posts', PostsController::class)
        ->except(['show'])
        ->names('admin.posts');

    Route::resource('/admin/services', ServicesController::class)
        ->except(['show'])
        ->names('admin.services');

    Route::resource('/admin/service-orders', ServiceOrdersController::class)
        ->except(['show'])
        ->names('admin.service-orders');

    Route::resource('/admin/project-tags', ProjectsTagsController::class)
        ->except(['show'])
        ->names('admin.project-tags');

    Route::resource('/admin/project-categories', ProjectsCategoriesController::class)
        ->except(['show'])
        ->names('admin.project-categories');

    Route::resource('/admin/projects', ProjectsController::class)
        ->except(['show'])
        ->names('admin.projects');

    Route::resource('/admin/product-tags', ProductsTagsController::class)
        ->except(['show'])
        ->names('admin.product-tags');

    Route::resource('/admin/product-categories', ProductsCategoriesController::class)
        ->except(['show'])
        ->names('admin.product-categories');

    Route::resource('/admin/products', ProductsController::class)
        ->except(['show'])
        ->names('admin.products');

    Route::resource('/admin/attributes', AttributesController::class)
        ->except(['show'])
        ->names('admin.attributes');
});
