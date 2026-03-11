<?php

namespace Modules\Tenants\Services;

use Modules\Tenants\Models\Tenant;
use Modules\Tenants\Models\Domain;
use Illuminate\Support\Facades\DB;
use Exception;

class TenantService
{

    public function create(array $data): Tenant
    {
        try {

            DB::beginTransaction();

            $database = 'tenant_' . $data['slug'];

            $tenant = Tenant::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'database' => $database
            ]);

            Domain::create([
                'tenant_id' => $tenant->id,
                'domain' => $data['domain']
            ]);

            DB::statement("CREATE DATABASE `$database`");

            app(TenantMigrationService::class)
                ->runMigrations($database);

            DB::commit();

            return $tenant;
        } catch (Exception $e) {

            DB::rollBack();

            throw new Exception(
                "Tenant creation failed: " . $e->getMessage()
            );
        }
    }
}
