<?php

namespace Modules\Tenants\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Modules\Tenants\Models\Tenant;

class TenantDatabaseService
{
    /*
    |--------------------------------------------------------------------------
    | Create Tenant Database
    |--------------------------------------------------------------------------
    */

    public function createDatabase(string $database): void
    {
        /*
        |--------------------------------------------------------------------------
        | Use SYSTEM connection (root/admin)
        |--------------------------------------------------------------------------
        */

        $system = DB::connection('system');

        $schemaExists = $system
            ->table('information_schema.schemata')
            ->where('schema_name', $database)
            ->exists();

        if (!$schemaExists) {

            $created = $system->statement(
                "CREATE DATABASE IF NOT EXISTS `$database`
                CHARACTER SET utf8mb4
                COLLATE utf8mb4_unicode_ci"
            );

            if ($created === false) {
                throw new \Exception("Failed to create tenant schema [$database].");
            }

            $schemaExists = $system
                ->table('information_schema.schemata')
                ->where('schema_name', $database)
                ->exists();

            if (!$schemaExists) {
                throw new \Exception("Tenant schema [$database] was not created.");
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Run Migrations
        |--------------------------------------------------------------------------
        */

        app(TenantMigrationService::class)->runMigrations($database);
    }

    /*
    |--------------------------------------------------------------------------
    | Set Tenant Connection Dynamically
    |--------------------------------------------------------------------------
    */

    protected function setTenantConnection(Tenant $tenant): void
    {
        tenant_connect($tenant);
    }

    /*
    |--------------------------------------------------------------------------
    | Migrate Tenant
    |--------------------------------------------------------------------------
    */

    public function migrate(string $database): void
    {
        $tenant = Tenant::where('database', $database)->firstOrFail();

        $this->setTenantConnection($tenant);

        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Seed Tenant
    |--------------------------------------------------------------------------
    */

    public function seed(string $database): void
    {
        $tenant = Tenant::where('database', $database)->firstOrFail();

        $this->setTenantConnection($tenant);

        // Bind tenant for seeders
        app()->instance('tenant', $tenant);

        Artisan::call('db:seed', [
            '--database' => 'tenant',
            '--class' => 'TenantDatabaseSeeder',
            '--force' => true
        ]);
    }
}
