<?php

namespace App\Support;

class MediaPathGenerator
{
    /*
    |--------------------------------------------------------------------------
    | Generate Storage Path
    |--------------------------------------------------------------------------
    | Creates tenant-aware storage paths.
    |
    | Example results:
    |
    | tenants/12/articles
    | tenants/12/services/gallery
    | platform/logos
    |
    */

    public static function generate(string $directory, ?string $collection = null): string
    {
        $base = tenant_id() === 'central'
            ? 'platform'
            : 'tenants/' . tenant_id();

        $path = $base . '/' . trim($directory, '/');

        if ($collection) {
            $path .= '/' . trim($collection, '/');
        }

        return $path;
    }
}