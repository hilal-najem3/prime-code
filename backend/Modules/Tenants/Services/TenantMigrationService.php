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
            Config::set('database.connections.tenant.database', $database);

            DB::purge('tenant');
            DB::reconnect('tenant');

            /*
        |--------------------------------------------------------------------------
        | Drop all tables from tenant database
        |--------------------------------------------------------------------------
        */

            $tables = DB::connection('tenant')
                ->select('SHOW TABLES');

            $dbName = DB::connection('tenant')->getDatabaseName();

            $key = "Tables_in_{$dbName}";

            DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($tables as $table) {
                DB::connection('tenant')->statement("DROP TABLE IF EXISTS `{$table->$key}`");
            }

            DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=1');

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

            /*
        |--------------------------------------------------------------------------
        | Run each migration file in order
        |--------------------------------------------------------------------------
        */

            foreach ($migrationFiles as $file) {

                $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file);

                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => $relativePath,
                    '--force' => true,
                ]);
            }
        } catch (\Throwable $e) {
            throw new \Exception(
                "Tenant migration failed: " . $e->getMessage()
            );
        }
    }
}