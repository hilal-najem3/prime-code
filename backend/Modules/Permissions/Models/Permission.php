<?php

namespace Modules\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'module',
        'active'
    ];

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'roles_permissions',
            'permission_id',
            'role_id'
        );
    }
}