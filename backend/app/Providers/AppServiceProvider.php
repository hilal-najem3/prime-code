<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach (glob(base_path('Modules/*/*ServiceProvider.php')) as $provider) {

            $class = str_replace(
                [base_path() . '/', '.php', '/'],
                ['', '', '\\'],
                $provider
            );

            $this->app->register($class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}