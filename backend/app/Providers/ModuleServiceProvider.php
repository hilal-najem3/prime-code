<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

abstract class ModuleServiceProvider extends ServiceProvider
{
    protected string $module;

    public function boot(): void
    {
        $modulePath = base_path("Modules/{$this->module}");

        /*
        |--------------------------------------------------------------------------
        | Load module migrations
        |--------------------------------------------------------------------------
        */

        $this->loadMigrationsFrom("{$modulePath}/Migrations");

        /*
        |--------------------------------------------------------------------------
        | Load module routes
        |--------------------------------------------------------------------------
        */

        if (file_exists("{$modulePath}/Routes/api.php")) {
            $this->loadRoutesFrom("{$modulePath}/Routes/api.php");
        }
    }
}