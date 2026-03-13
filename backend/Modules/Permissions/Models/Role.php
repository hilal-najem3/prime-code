<?php

namespace Modules\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Role extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'roles_permissions',
            'role_id',
            'permission_id'
        );
    }

    public function users()
    {
        return $this->belongsToMany(
            \Modules\Auth\Models\User::class,
            'users_roles'
        );
    }

    public static function booted()
    {
        static::saved(function () {
            Cache::forget("tenant_" . app('tenant')->id . "_permissions");
        });
    }
}