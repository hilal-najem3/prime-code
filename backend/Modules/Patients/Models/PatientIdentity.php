<?php

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Media\Models\Media;

class PatientIdentity extends Model
{
    use SoftDeletes;

    // Set database to tenant connection
    protected $connection = 'tenant';

    protected $fillable = [
        'patient_id',
        'type',
        'number',
        'issued_at',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }
}