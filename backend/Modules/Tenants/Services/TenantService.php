<?php

namespace Modules\Tenants\Services;

use Modules\Tenants\Models\Tenant;
use Modules\Tenants\Models\Domain;

class TenantService
{
    public function create(array $data)
    {
        $database = "tenant_" . $data['slug'];

        $tenant = Tenant::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'database' => $database
        ]);

        Domain::create([
            'tenant_id' => $tenant->id,
            'domain' => $data['domain']
        ]);

        $dbService = new TenantDatabaseService();

        $dbService->createDatabase($database);
        $dbService->migrate($database);
        $dbService->seed($database);

        return $tenant;
    }
}
