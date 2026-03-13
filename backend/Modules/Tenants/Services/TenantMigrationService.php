<?php

namespace Modules\Tenants\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Exception;

class TenantMigrationService
{
    /*
    |--------------------------------------------------------------------------
    | Run Tenant Migrations
    |--------------------------------------------------------------------------
    |
    | Dynamically scans all module migration directories and runs them
    | against the tenant database.
    |
    */

    public function runMigrations(string $database): void
    {
        try {

            dump("Running migrations for tenant database: {$database}");

            Config::set('database.connections.tenant.database', $database);

            DB::purge('tenant');
            DB::reconnect('tenant');

            /*
        |--------------------------------------------------------------------------
        | Collect all module migration files
        |--------------------------------------------------------------------------
        */

            $migrationFiles = glob(base_path('Modules/*/Migrations/*.php'));

            /*
        |--------------------------------------------------------------------------
        | Sort migrations by timestamp filename
        |--------------------------------------------------------------------------
        */

            usort($migrationFiles, function ($a, $b) {
                return strcmp(basename($a), basename($b));
            });

            dump("Ordered migrations:", array_map('basename', $migrationFiles));

            /*
        |--------------------------------------------------------------------------
        | Run each migration file in order
        |--------------------------------------------------------------------------
        */

            foreach ($migrationFiles as $file) {

                $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file);

                dump("Running migration:", $relativePath);

                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => $relativePath,
                    '--force' => true,
                ]);

                dump(Artisan::output());
            }
        } catch (\Throwable $e) {

            dump($e->getMessage());

            throw new \Exception(
                "Tenant migration failed: " . $e->getMessage()
            );
        }
    }
}