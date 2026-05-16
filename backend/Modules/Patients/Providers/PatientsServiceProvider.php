<?php

namespace Modules\Patients\Providers;

use App\Providers\ModuleServiceProvider;
use App\Support\MediaConversionRegistry;
use Modules\Patients\Models\PatientIdentity;

class PatientsServiceProvider extends ModuleServiceProvider
{
    protected string $module = 'Patients';

    public function boot(): void
    {
        parent::boot();

        // Register custom media variants for patient identity documents.
        // Only generate base, thumb, and medium variants to optimize storage and processing.
        MediaConversionRegistry::register(PatientIdentity::class, [
            'thumb'  => 150,
            'medium' => 400,
        ]);
    }
}
