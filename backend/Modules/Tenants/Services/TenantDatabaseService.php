<?php

namespace Modules\Tenants\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class TenantDatabaseService
{
    public function createDatabase(string $database)
    {
        DB::statement(
            "CREATE DATABASE IF NOT EXISTS `$database` 
        CHARACTER SET utf8mb4 
        COLLATE utf8mb4_unicode_ci"
        );
    }

    public function migrate(string $database)
    {
        Config::set('database.connections.tenant.database', $database);

        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true
        ]);
    }

    public function seed(string $database)
    {
        Config::set('database.connections.tenant.database', $database);

        Artisan::call('db:seed', [
            '--database' => 'tenant',
            '--class' => 'TenantDatabaseSeeder',
            '--force' => true
        ]);
    }
}