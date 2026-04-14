<?php

namespace Modules\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Platform Database Connection
    |--------------------------------------------------------------------------
    */

    protected $connection = 'mysql';
    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'slug',
        'module',
        'active'
    ];

    /*
    |--------------------------------------------------------------------------
    | Roles Relationship (RARELY USED)
    |--------------------------------------------------------------------------
    | Only works safely if you explicitly control connection
    */

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'roles_permissions',
            'permission_id',
            'role_id'
        )->using(RolePermissionPivot::class);
    }
}
