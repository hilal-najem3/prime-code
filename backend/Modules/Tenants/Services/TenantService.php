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

        $tenant = Tenant::withTrashed()->firstOrNew([
            'slug' => $data['slug'],
        ]);

        $tenant->fill([
            'name' => $data['name'],
            'database' => $database,
            'domain' => $data['domain'],
        ]);

        $tenant->save();

        if ($tenant->trashed()) {
            $tenant->restore();
        }

        $domain = Domain::withTrashed()->firstOrNew([
            'domain' => $data['domain']
        ]);

        $domain->tenant_id = $tenant->id;
        $domain->save();

        if ($domain->trashed()) {
            $domain->restore();
        }

        $dbService = new TenantDatabaseService();

        $dbService->createDatabase($database);

        $dbService->seed($database);

        return $tenant;
    }
}
