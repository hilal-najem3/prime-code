<?php

namespace Modules\Tenants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domain extends Model
{
    use HasUuids,
        SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'domain'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
