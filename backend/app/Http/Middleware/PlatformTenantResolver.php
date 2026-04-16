<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Tenants\Models\Tenant;

class PlatformTenantResolver
{
    public function handle(Request $request, Closure $next)
    {
        $tenantId = $request->route('tenant') ?? $request->route('tenant_id');

        if ($tenantId) {
            $tenant = Tenant::findOrFail($tenantId);

            if (!$tenant->isActive()) {
                abort(403, 'Tenant is not active.');
            }

            tenant_connect($tenant);
        }

        return $next($request);
    }
}