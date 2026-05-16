<?php

namespace Modules\Permissions\Services;

use Modules\Permissions\Models\Permission;

class PermissionService
{
    /*
    |--------------------------------------------------------------------------
    | Find Permission
    |--------------------------------------------------------------------------
    */

    public function find(string $id): Permission
    {
        return Permission::findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Permissions
    |--------------------------------------------------------------------------
    */

    public function get()
    {
        return Permission::query()
            ->where('active', true)
            ->orderBy('module')   // group modules
            ->orderBy('slug')     // consistent inside module
            ->orderByRaw("
                    CASE
                        WHEN slug LIKE '%.index' THEN 1
                        WHEN slug LIKE '%.show' THEN 2
                        WHEN slug LIKE '%.store' THEN 3
                        WHEN slug LIKE '%.update' THEN 4
                        WHEN slug LIKE '%.destroy' THEN 5
                        ELSE 6
                    END
                ")
            ->get();
    }
}
