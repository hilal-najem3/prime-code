<?php

namespace Modules\Tenants\Services;

use Modules\Tenants\Models\Tenant;
use Modules\Modules\Models\Module;

class TenantModuleService
{
    /**
     * Get modules assigned to tenant
     */
    public function getModules(Tenant $tenant)
    {
        return $tenant->modules()->get();
    }

    /**
     * Sync modules (replace all)
     */
    public function syncModules(Tenant $tenant, array $moduleIds)
    {
        $modules = Module::whereIn('id', $moduleIds)->pluck('id');

        $tenant->modules()->sync($modules);

        return $tenant->modules()->get();
    }
}