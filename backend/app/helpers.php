<?php

use Modules\Tenants\Support\TenantContext;

function tenant()
{
    return app(TenantContext::class)->get();
}

function tenant_id()
{
    return app(TenantContext::class)->id();
}