<?php

namespace Modules\Tenants\Services;

use Modules\Tenants\Models\Tenant;
use Modules\Tenants\Models\Domain;

class TenantService
{
    public function create(array $data)
    {
        $database = $data['database'] ?? "tenant_" . $data['slug'];

        $tenant = Tenant::firstOrCreate(
            ['slug' => $data['slug']],
            [
                'name' => $data['name'],
                'database' => $database,
                "domain" => $data['domain']
            ]
        );

        Domain::updateOrCreate([
            'domain' => $data['domain']
        ], [
            'tenant_id' => $tenant->id
        ]);

        $dbService = new TenantDatabaseService();

        if (config('app.saas_db_mode') === 'auto') {
            $dbService->createDatabase($database);
        }

        app(\Modules\Tenants\Services\TenantMigrationService::class)
            ->runMigrations($database);

        $dbService->seed($database);

        return $tenant;
    }
}