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
        'active',
        "domain"
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    protected $attributes = [
        'active' => true
    ];

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }
}