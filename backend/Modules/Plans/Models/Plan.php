<?php

namespace Modules\Plans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasUuids,
        SoftDeletes;

    protected $connection = 'mysql';

    protected $fillable = [
        'name',
        'slug',
        'price',
        'description',
        'active',
    ];

    public function modules()
    {
        return $this->belongsToMany(
            \Modules\Modules\Models\Module::class,
            'plan_modules',
            'plan_id',
            'module_id'
        );
    }
}
