<?php

namespace App\Support;

class MediaAllowedModels
{
    /**
     * Models allowed for media attachment.
     */
    public static function all(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Patients
            |--------------------------------------------------------------------------
            */

            \Modules\Patients\Models\PatientIdentity::class,

        ];
    }

    /**
     * Check if model is allowed.
     */
    public static function contains(string $model): bool
    {
        return in_array($model, self::all(), true);
    }
}