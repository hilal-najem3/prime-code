<?php

namespace Modules\Pages\Blocks;

use Modules\Pages\Blocks\Contracts\BlockInterface;

class HeroBlock implements BlockInterface
{
    public function type(): string
    {
        return 'hero';
    }

    public function normalizeSettings(array $settings): array
    {
        return array_merge([
            'height' => 'auto',
            'overlay' => false,
            'overlay_color' => 'dark',
        ], $settings);
    }

    public function normalizeData(array $data): array
    {
        return $this->mergeRecursive([
            'title' => ['en' => ''],
            'subtitle' => ['en' => ''],
            'background_image' => null,
            'button' => [
                'text' => ['en' => ''],
                'url' => '#',
            ],
        ], $data);
    }

    protected function mergeRecursive(array $defaults, array $data): array
    {
        foreach ($defaults as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->mergeRecursive(
                    $value,
                    $data[$key] ?? []
                );
            } else {
                $data[$key] = $data[$key] ?? $value;
            }
        }

        return $data;
    }
}