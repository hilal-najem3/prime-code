<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Tenants\Models\Tenant;
use Modules\Tenants\Services\TenantMigrationService;

class TenantMigrateCommand extends Command
{
    protected $signature = 'tenant:migrate {tenant}';

    protected $description = 'Run migrations for a single tenant';

    public function handle()
    {
        $slug = $this->argument('tenant');

        $tenant = Tenant::where('slug', $slug)->first();

        if (!$tenant) {
            $this->error("Tenant not found.");
            return;
        }

        $this->info("Running migrations for tenant: {$tenant->slug}");

        app(TenantMigrationService::class)
            ->runMigrations($tenant->database);

        $this->info("Tenant migrations completed.");
    }
}