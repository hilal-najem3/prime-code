<?php

function tenant()
{
    return app()->bound('tenant') ? app('tenant') : null;
}

function tenant_id()
{
    $tenant = tenant();
    $tenantId = $tenant?->id ?? 'central';
    return $tenantId;
}