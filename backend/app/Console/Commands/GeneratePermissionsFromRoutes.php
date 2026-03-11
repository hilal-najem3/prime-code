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

            if (!$name) continue;

            $current[] = $name;

            Permission::firstOrCreate(
                ['slug' => $name],
                [
                    'name' => $this->format($name),
                    'module' => $this->module($name)
                ]
            );
        }

        Permission::whereNotIn('slug', $current)->delete();

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
