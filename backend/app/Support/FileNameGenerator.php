<?php

namespace App\Support;

use Illuminate\Support\Str;

class FileNameGenerator
{
    public static function generate(string $originalName): string
    {
        $name = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        $slug = Str::slug($name);

        $random = random_int(1000, 9999);

        return "{$slug}-{$random}.{$extension}";
    }
}