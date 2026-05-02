<?php

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Models\User;
use Modules\Patients\Models\PatientIdentity;

class Patient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'phone',
        'phone_secondary',
        'email',
        'address',
        'blood_type',
        'allergies',
        'status',
        'notes',
    ];

    protected $casts = [
        'address' => 'array',
        'date_of_birth' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function identities()
    {
        return $this->hasMany(PatientIdentity::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors (Optional but useful)
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}