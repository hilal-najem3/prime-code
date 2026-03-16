<?php

namespace App\Support;

class MediaConversionRegistry
{
    protected static array $conversions = [];

    protected static array $default = [
        'thumb'   => 150,
        'medium'  => 400,
        'large'   => 1200,
        'avatar'  => 200,
        'card'    => 400,
        'gallery' => 800,
    ];

    public static function register(string $model, array $variants): void
    {
        self::$conversions[$model] = $variants;
    }

    public static function get(?string $model): array
    {
        if (!$model) {
            return self::$default;
        }

        return self::$conversions[$model] ?? self::$default;
    }
}