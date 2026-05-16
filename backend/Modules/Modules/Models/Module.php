<?php

namespace Modules\Modules\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasUuids,
        SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'enabled',
    ];

    public function tenants()
    {
        return $this->belongsToMany(
            \Modules\Tenants\Models\Tenant::class,
            'tenant_modules'
        );
    }
}
