<?php

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Models\User;
use Modules\Patients\Models\PatientIdentity;

class Patient extends Model
{
    use SoftDeletes;

    // Set database to tenant connection
    protected $connection = 'tenant';

    protected $fillable = [
        'user_id',
        'allow_login',
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
        'allow_login' => 'boolean',
    ];

    protected $appends = [
        'full_name',
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
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function scopeWhereFullName(Builder $query, string $firstName, string $lastName): Builder
    {
        return $query
            ->where('first_name', trim($firstName))
            ->where('last_name', trim($lastName));
    }
}
