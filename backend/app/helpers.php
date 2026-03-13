<?php

function tenant()
{
    return app()->bound('tenant') ? app('tenant') : null;
}

function tenant_id()
{
    return tenant()?->id;
}