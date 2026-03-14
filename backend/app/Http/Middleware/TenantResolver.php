<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Tenants\Models\Domain;

class TenantResolver
{
    public function handle(Request $request, Closure $next)
    {
        $domain = $request->getHost();

        /*
        |--------------------------------------------------------------------------
        | Platform Domain Bypass
        |--------------------------------------------------------------------------
        */

        if ($domain === config('app.platform_domain')) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Tenant From Domain (Cached)
        |--------------------------------------------------------------------------
        */

        $domainModel = cache()->remember(
            "tenant_domain_{$domain}",
            3600,
            fn() => Domain::with('tenant')
                ->where('domain', $domain)
                ->first()
        );

        if (!$domainModel || !$domainModel->tenant) {
            abort(404, 'Tenant not found.');
        }

        $tenant = $domainModel->tenant;

        if (!$tenant->isActive()) {
            abort(403, 'Tenant account is not active.');
        }

        /*
        |--------------------------------------------------------------------------
        | Configure Tenant Database
        |--------------------------------------------------------------------------
        */

        Config::set('database.connections.tenant.database', $tenant->database);

        DB::purge('tenant');
        DB::reconnect('tenant');

        DB::setDefaultConnection('tenant');

        app()->instance('tenant', $tenant);

        return $next($request);
    }
}