<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenAuditLog extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    |
    | Explicitly defining the table improves clarity when models
    | are placed inside modules.
    |
    */

    protected $table = 'token_audit_logs';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'user_id',
        'action',
        'ip_address',
        'device'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }
}