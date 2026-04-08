<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Modules\Tenants\Models\Tenant;

class TenantSeedCommand extends Command
{
    protected $signature = 'tenant:seed
                            {tenant : Tenant slug}
                            {--seeder= : Specific seeder class (FQCN or class name)}';

    protected $description = 'Seed a single tenant database using module seeders';

    public function handle(): int
    {
        $slug = $this->argument('tenant');
        $seederOption = $this->option('seeder');

        $tenant = Tenant::where('slug', $slug)->first();

        if (!$tenant) {
            $this->error("Tenant not found for slug [{$slug}].");
            return self::FAILURE;
        }

        tenant_connect($tenant);
        app()->instance('tenant', $tenant);

        $this->info("Seeding tenant: {$tenant->slug}");

        $seeders = $seederOption
            ? $this->resolveSpecificSeeder($seederOption)
            : $this->discoverModuleSeeders();

        $this->line('Seeders to run:');
        foreach ($seeders as $seeder) {
            $this->line("- {$seeder}");
        }

        if (empty($seeders)) {
            $this->warn('No seeders found to run.');
            return self::SUCCESS;
        }

        foreach ($seeders as $seederClass) {
            $this->line("Running seeder: {$seederClass}");

            Artisan::call('db:seed', [
                '--database' => 'tenant',
                '--class' => $seederClass,
                '--force' => true,
            ]);

            $output = trim(Artisan::output());
            if ($output !== '') {
                $this->line($output);
            }
        }

        $this->info('Tenant seeding completed.');

        return self::SUCCESS;
    }

    protected function discoverModuleSeeders(): array
    {
        $files = glob(base_path('Modules/*/Seeders/*Seeder.php')) ?: [];

        sort($files);

        return array_values(array_filter(array_map(
            fn(string $file) => $this->classFromFile($file),
            $files
        )));
    }

    protected function resolveSpecificSeeder(string $input): array
    {
        // Accept file-style input like Modules/Languages/Seeders/LanguagesSeeder.php
        if (str_contains($input, '/') || str_contains($input, '\\') || str_ends_with($input, '.php')) {
            $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $input);
            $file = str_starts_with($normalized, base_path())
                ? $normalized
                : base_path($normalized);

            $classFromFile = $this->classFromFile($file);

            if ($classFromFile) {
                return [$classFromFile];
            }
        }

        // If a fully-qualified class is provided and exists, use it directly.
        if (class_exists($input)) {
            return [$input];
        }

        $allSeeders = $this->discoverModuleSeeders();

        $matches = array_values(array_filter($allSeeders, function (string $class) use ($input) {
            return strcasecmp(class_basename($class), class_basename($input)) === 0;
        }));

        if (count($matches) === 1) {
            return [$matches[0]];
        }

        if (count($matches) > 1) {
            $this->error("Seeder [{$input}] is ambiguous. Use full class name.");
            $this->line('Matches:');
            foreach ($matches as $match) {
                $this->line("- {$match}");
            }

            return [];
        }

        $this->error("Seeder [{$input}] was not found.");
        return [];
    }

    protected function classFromFile(string $file): ?string
    {
        if (!is_file($file)) {
            return null;
        }

        $contents = file_get_contents($file);

        if ($contents === false) {
            return null;
        }

        preg_match('/^\s*namespace\s+([^;]+);/m', $contents, $namespaceMatches);
        preg_match('/^\s*class\s+([A-Za-z_][A-Za-z0-9_]*)/m', $contents, $classMatches);

        if (empty($classMatches[1])) {
            return null;
        }

        $class = $classMatches[1];
        $namespace = $namespaceMatches[1] ?? null;
        $fqn = $namespace ? trim($namespace) . '\\' . $class : $class;

        if (!class_exists($fqn, false)) {
            require_once $file;
        }

        return $fqn;
    }
}
