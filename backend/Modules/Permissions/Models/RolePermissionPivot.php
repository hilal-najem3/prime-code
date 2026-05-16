<?php

namespace Modules\Permissions\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RolePermissionPivot extends Pivot
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = null;
    protected $table = 'roles_permissions';

    /*
    |--------------------------------------------------------------------------
    | Force Tenant Connection
    |--------------------------------------------------------------------------
    | Pivot MUST always use tenant DB
    */

    public function getConnectionName()
    {
        return config('database.default');
        // after tenant_connect() this becomes tenant DB
    }

    public $timestamps = false;
}
