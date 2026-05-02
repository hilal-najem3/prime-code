<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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

        /*
        |--------------------------------------------------------------------------
        | Register module policies
        |--------------------------------------------------------------------------
        */

        $this->registerPolicies("{$modulePath}/Policies");
    }

    /**
     * Auto-discover and register policies from the module's Policies directory
     */
    private function registerPolicies(string $policiesPath): void
    {
        if (!is_dir($policiesPath)) {
            return;
        }

        static $registered = [];

        if (isset($registered[$policiesPath])) {
            return;
        }

        foreach (glob("{$policiesPath}/**/*.php") as $file) {
            $filename = basename($file, '.php');

            if (!str_ends_with($filename, 'Policy')) {
                continue;
            }

            $modelName = str_replace('Policy', '', $filename);

            $policyClass = "Modules\\{$this->module}\\Policies\\{$filename}";
            $modelClass  = "Modules\\{$this->module}\\Models\\{$modelName}";

            if (class_exists($policyClass)) {
                $modelClass = $policyClass::$model ?? null;

                if ($modelClass && class_exists($modelClass)) {
                    Gate::policy($modelClass, $policyClass);
                }
            }
        }

        $registered[$policiesPath] = true;
    }
}