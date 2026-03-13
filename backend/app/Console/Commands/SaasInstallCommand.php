<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Modules\Tenants\Services\TenantService;

class SaasInstallCommand extends Command
{
    protected $signature = 'saas:install 
                            {--tenant=demo}
                            {--domain=demo.local}';

    protected $description = 'Install SaaS platform and create first tenant';

    public function handle()
    {
        $this->info("Starting SaaS installation...");

        /*
        |--------------------------------------------------------------------------
        | Platform Migrations
        |--------------------------------------------------------------------------
        */

        $this->info("Migrating platform database...");

        Artisan::call('migrate:fresh', ['--force' => true]);

        /*
        |--------------------------------------------------------------------------
        | Platform Seeders
        |--------------------------------------------------------------------------
        */

        $this->info("Seeding platform...");

        Artisan::call('db:seed', [
            '--class' => 'DatabaseSeeder',
            '--force' => true
        ]);

        /*
        |--------------------------------------------------------------------------
        | Generate Permissions
        |--------------------------------------------------------------------------
        */

        Artisan::call('permissions:generate');

        /*
        |--------------------------------------------------------------------------
        | Create First Tenant
        |--------------------------------------------------------------------------
        */

        $slug = $this->option('tenant');
        $domain = $this->option('domain') ?? "demo.local";

        $this->info("Creating tenant: {$slug}");

        app(TenantService::class)->create([
            'name' => ucfirst($slug),
            'slug' => $slug,
            'domain' => $domain
        ]);

        $this->info("SaaS installation complete.");
    }
}