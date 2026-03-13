<?php

namespace Modules\Tenants\Services;

use Modules\Tenants\Models\Tenant;
use Modules\Tenants\Models\Domain;

class TenantService
{
    public function create(array $data)
    {
        $database = "tenant_" . $data['slug'];

        $tenant = Tenant::firstOrCreate(
            ['slug' => $data['slug']],
            [
                'name' => $data['name'],
                'database' => $database,
                'domain' => $data['domain']
            ]
        );

        Domain::create([
            'tenant_id' => $tenant->id,
            'domain' => $data['domain']
        ]);

        $dbService = new TenantDatabaseService();

        $dbService->createDatabase($database);

        $migrationService = new TenantMigrationService();
        $migrationService->runMigrations($database);

        $dbService->seed($database);

        return $tenant;
    }
}