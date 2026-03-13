<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Tenants\Services\TenantService;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Generate Permissions (Platform DB)
        |--------------------------------------------------------------------------
        */

        $this->call([
            PermissionSeeder::class,
        ]);
    }
}