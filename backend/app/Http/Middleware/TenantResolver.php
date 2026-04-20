<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Tenants\Models\Domain;
use Modules\Tenants\Support\TenantContext;

class TenantResolver
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('OPTIONS')) {
            return response()->noContent();
        }

        $domain = $request->getHost();
        Log::info("Resolving tenant for domain: {$domain}");

        /*
        |--------------------------------------------------------------------------
        | Normalize Admin Subdomain
        |--------------------------------------------------------------------------
        */

        if (str_starts_with($domain, 'admin.')) {
            $domain = substr($domain, 6); // remove "admin."
        }

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
        Log::info("Connecting to tenant database: {$tenant->database}");
        tenant_connect($tenant);

        app(TenantContext::class)->set($tenant);

        Log::info("Tenant resolved: {$tenant->name} ({$tenant->id})");
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
