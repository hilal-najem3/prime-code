<?php

namespace Modules\Auth\Providers;

use App\Providers\ModuleServiceProvider;

class AuthServiceProvider extends ModuleServiceProvider
{
    protected string $module = 'Auth';

    public function boot(): void
    {
        parent::boot();

        // MediaConversionRegistry::register(User::class, [
        //     'thumb' => 150,
        //     'medium' => 400,
        //     'hero' => 1200,
        // ]);
    }
}