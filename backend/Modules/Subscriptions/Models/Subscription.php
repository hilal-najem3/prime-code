<?php

namespace Modules\Subscriptions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql'; // platform DB

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
    ];

    public function plan()
    {
        return $this->belongsTo(
            \Modules\Plans\Models\Plan::class
        );
    }
}