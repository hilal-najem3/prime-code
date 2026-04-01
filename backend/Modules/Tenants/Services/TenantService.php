<?php

namespace Modules\Tenants\Services;

use Modules\Tenants\Models\Tenant;
use Modules\Tenants\Models\Domain;

class TenantService
{
    public function get(array $filters = [], array $queryParams = [])
    {
        $perPage = $filters['per_page'] ?? null;
        $search = trim($filters['search'] ?? '');
        $sort = $filters['sort'] ?? 'id';
        $direction = strtolower($filters['direction'] ?? 'asc');

        $query = Tenant::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('domain', 'like', "%{$search}%")
                        ->orWhere('database', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction);

        return $perPage
            ? $query->paginate($perPage)->appends($queryParams)
            : $query->get();
    }

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
