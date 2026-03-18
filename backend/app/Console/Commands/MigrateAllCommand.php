<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Modules\Tenants\Models\Tenant;

class MigrateAllCommand extends Command
{
    protected $signature = 'saas:migrate-all';

    protected $description = 'Run platform migrations, tenant migrations, and regenerate permissions';

    public function handle()
    {
        $this->info("Starting full SaaS migration process...");

        /*
        |--------------------------------------------------------------------------
        | Platform Migrations
        |--------------------------------------------------------------------------
        */

        $this->info("Running platform migrations...");

        Artisan::call('migrate', [
            '--force' => true
        ]);

        $this->line(Artisan::output());

        /*
        |--------------------------------------------------------------------------
        | Tenant Migrations
        |--------------------------------------------------------------------------
        */

        $this->info("Running tenant migrations...");

        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {

            $this->line("Migrating tenant: {$tenant->slug}");

            Artisan::call('tenant:migrate', [
                'tenant' => $tenant->slug
            ]);

            $this->line(Artisan::output());
        }

        /*
        |--------------------------------------------------------------------------
        | Permission Synchronization
        |--------------------------------------------------------------------------
        */

        $this->info("Generating permissions from routes...");

        Artisan::call('permissions:generate');

        $this->line(Artisan::output());

        $this->info("All migrations completed successfully.");
    }
}