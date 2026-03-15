<?php

namespace Modules\Tenants\Support;

use Modules\Tenants\Models\Tenant;

class TenantContext
{
    protected ?Tenant $tenant = null;

    /*
    |--------------------------------------------------------------------------
    | Set Tenant
    |--------------------------------------------------------------------------
    */

    public function set(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    /*
    |--------------------------------------------------------------------------
    | Get Tenant
    |--------------------------------------------------------------------------
    */

    public function get(): ?Tenant
    {
        return $this->tenant;
    }

    /*
    |--------------------------------------------------------------------------
    | Get Tenant ID
    |--------------------------------------------------------------------------
    */

    public function id(): string|int
    {
        return $this->tenant?->id ?? 'central';
    }

    /*
    |--------------------------------------------------------------------------
    | Clear Context
    |--------------------------------------------------------------------------
    */

    public function clear(): void
    {
        $this->tenant = null;
    }
}