<?php

namespace Modules\Tenants\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class TenantDatabaseService
{
    public function createDatabase(string $database): void
    {
        Config::set(
            'database.connections.tenant.database',
            config('database.connections.mysql.database')
        );

        DB::purge('tenant');
        DB::reconnect('tenant');

        $schemaExists = DB::connection('tenant')
            ->table('information_schema.schemata')
            ->where('schema_name', $database)
            ->exists();

        if (!$schemaExists) {
            $created = DB::connection('tenant')->statement(
                "CREATE DATABASE IF NOT EXISTS `$database`
                CHARACTER SET utf8mb4
                COLLATE utf8mb4_unicode_ci"
            );

            if ($created === false) {
                throw new \Exception("Failed to create tenant schema [$database].");
            }

            $schemaExists = DB::connection('tenant')
                ->table('information_schema.schemata')
                ->where('schema_name', $database)
                ->exists();

            if (!$schemaExists) {
                throw new \Exception("Tenant schema [$database] was not created.");
            }
        }

        Config::set('database.connections.tenant.database', $database);

        DB::purge('tenant');
        DB::reconnect('tenant');

        app(TenantMigrationService::class)->runMigrations($database);
    }

    public function migrate(string $database): void
    {
        Config::set('database.connections.tenant.database', $database);

        DB::purge('tenant');
        DB::reconnect('tenant');

        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true
        ]);
    }

    public function seed(string $database): void
    {
        Config::set('database.connections.tenant.database', $database);
        DB::purge('tenant');
        DB::reconnect('tenant');

        // Resolve the tenant instance
        $tenant = \Modules\Tenants\Models\Tenant::where('database', $database)->first();

        if ($tenant) {
            app()->instance('tenant', $tenant);
        }

        Artisan::call('db:seed', [
            '--database' => 'tenant',
            '--class' => 'TenantDatabaseSeeder',
            '--force' => true
        ]);
    }
}
