<?php

namespace Modules\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Role extends Model
{
    use HasUuids,
        SoftDeletes;

    protected $fillable = [
        'name',
        'slug'
    ];

    /*
    |--------------------------------------------------------------------------
    | Permissions Relationship
    |--------------------------------------------------------------------------
    | Cross-DB:
    | - Pivot = tenant DB
    | - Permission = platform DB
    */

    public function getPermissionsAttribute()
    {
        // Step 1: get permission IDs from tenant DB
        $permissionIds = \Illuminate\Support\Facades\DB::table('roles_permissions')
            ->where('role_id', $this->id)
            ->pluck('permission_id');

        if ($permissionIds->isEmpty()) {
            return collect();
        }

        // Step 2: fetch from platform DB
        return \Modules\Permissions\Models\Permission::query()
            ->whereIn('id', $permissionIds)
            ->get();
    }

    public function syncPermissions(array $permissionIds): void
    {
        DB::table('roles_permissions')
            ->where('role_id', $this->id)
            ->delete();

        if (empty($permissionIds)) {
            return;
        }

        $insertData = collect($permissionIds)->map(fn($id) => [
            'role_id' => $this->id,
            'permission_id' => $id,
        ]);

        DB::table('roles_permissions')->insert($insertData->toArray());
    }

    /*
    |--------------------------------------------------------------------------
    | Users Relationship
    |--------------------------------------------------------------------------
    */

    public function users()
    {
        return $this->belongsToMany(
            \Modules\Auth\Models\User::class,
            'users_roles'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cache Invalidation
    |--------------------------------------------------------------------------
    */

    public static function booted()
    {
        static::saved(function ($role) {

            if (!app()->bound('tenant')) {
                return;
            }

            $tenant = app(\Modules\Tenants\Support\TenantContext::class)->get();

            foreach ($role->users as $user) {
                Cache::forget("tenant_{$tenant->id}_user_permissions_{$user->id}");
            }

            // also clear role cache
            Cache::forget("tenant_{$tenant->id}_role_permissions_{$role->id}");
        });
    }
}
