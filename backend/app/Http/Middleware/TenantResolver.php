<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Tenants\Models\Domain;
use Modules\Tenants\Support\TenantContext;

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

        if (in_array($domain, config('app.platform_domains', []))) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Tenant
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

        app(TenantContext::class)->set($tenant);

        return $next($request);
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Tenant Context
    |--------------------------------------------------------------------------
    */

    public function terminate($request, $response)
    {
        DB::setDefaultConnection(config('database.default'));

        app(TenantContext::class)->clear();
    }
}