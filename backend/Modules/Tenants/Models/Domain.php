<?php

namespace Modules\Tenants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domain extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'domain'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
