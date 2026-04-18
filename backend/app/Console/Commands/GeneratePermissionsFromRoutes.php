<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Modules\Permissions\Models\Permission;

class GeneratePermissionsFromRoutes extends Command
{
    protected $signature = 'permissions:generate';

    protected $description = 'Generate permissions from routes';

    public function handle()
    {
        $routes = Route::getRoutes();

        $current = [];

        foreach ($routes as $route) {

            $name = $route->getName();

            if (
                !$name ||
                str_starts_with($name, 'sanctum') ||
                str_starts_with($name, 'ignition') ||
                str_starts_with($name, 'generated::') || // 👈 FIX
                !str_contains($name, '.') // 👈 must follow your naming convention
            ) {
                continue;
            }

            if (!$name) continue;

            $current[] = $name;

            $formatted_name = $this->format($name);
            $formatted_module = $this->module($name);

            Permission::updateOrCreate(
                ['slug' => $name],
                [
                    'name' => $formatted_name,
                    'module' => $formatted_module,
                    'active' => true
                ]
            );
        }

        Permission::whereNotIn('slug', $current)
            ->update(['active' => false]);

        $this->info("Permissions synced");
    }

    protected function format(string $slug)
    {
        return ucfirst(str_replace('.', ' ', $slug));
    }

    protected function module(string $slug)
    {
        return explode('.', $slug)[0];
    }
}
