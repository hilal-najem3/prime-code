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

            DB::beginTransaction();

            Config::set('database.connections.tenant.database', $database);

            DB::purge('tenant');
            DB::reconnect('tenant');

            $migrationPaths = glob(base_path('Modules/*/Migrations'));

            foreach ($migrationPaths as $path) {

                if (!is_dir($path)) {
                    continue;
                }

                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => str_replace(base_path() . '/', '', $path),
                    '--force' => true
                ]);
            }

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            throw new Exception(
                "Tenant migration failed: " . $e->getMessage()
            );
        }
    }
}
