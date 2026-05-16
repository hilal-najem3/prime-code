<?php

namespace Modules\Tenants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Tenant extends Model
{
    use HasUuids,
        SoftDeletes;

    protected $connection = 'mysql';

    protected $fillable = [
        'name',
        'slug',
        'db_username',
        'database',
        'db_password',
        'theme',
        'plan_id',
        'status',
        'domain'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    protected $attributes = [
        'status' => 'active'
    ];

    protected $hidden = [
        'db_password'
    ];

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' || $this->status === 'trial';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'trial']);
    }

    protected function dbPassword(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Crypt::decryptString($value) : null,
            set: fn($value) => $value ? Crypt::encryptString($value) : null,
        );
    }

    public function modules()
    {
        return $this->belongsToMany(
            \Modules\Modules\Models\Module::class,
            'tenant_modules'
        );
    }

    public function subscription()
    {
        return $this->hasOne(
            \Modules\Subscriptions\Models\Subscription::class
        )
            ->latestOfMany()
            ->with(['plan']);
    }
}
