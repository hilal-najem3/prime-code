<?php

namespace Modules\Tenants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'database',
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
}