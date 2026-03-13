<?php

namespace Modules\Auth\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Modules\Auth\Traits\HasPermissionsTrait;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory,
        Notifiable,
        HasApiTokens,
        HasPermissionsTrait,
        SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Eager Loading
    |--------------------------------------------------------------------------
    |
    | Roles are loaded automatically to reduce queries when checking
    | permissions and authorization.
    |
    */

    protected $with = ['roles'];

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Attributes
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'email',
        'password',
        'enabled'
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden Attributes
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
        'remember_token'
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed'
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function clearPermissionCache(): void
    {
        cache()->forget("user_permissions_{$this->id}");
    }

    public function isEnabled(): bool
    {
        return (bool) $this->enabled;
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}