<?php

namespace Modules\Pages\Services;

/*
|--------------------------------------------------------------------------
| Page Content Service
|--------------------------------------------------------------------------
|
| Responsible for:
| - Normalizing blocks structure
| - Applying defaults
| - Preventing invalid rendering
|
*/

class PageContentService
{
    /**
     * Normalize full page content
     */
    public function normalize(?array $content): array
    {
        if (!$content || !is_array($content)) {
            return [];
        }

        return collect($content)
            ->map(fn($block) => $this->normalizeBlock($block))
            ->filter() // remove invalid blocks
            ->values()
            ->toArray();
    }

    /**
     * Normalize a single block
     */
    protected function normalizeBlock(?array $block): ?array
    {
        if (!$block || empty($block['type'])) {
            return null;
        }

        $type = $block['type'];
        $variant = $block['variant'] ?? 'default';

        return [
            'type' => $type,
            'variant' => $variant,
            'settings' => $this->normalizeSettings($type, $block['settings'] ?? []),
            'data' => $this->normalizeData($type, $block['data'] ?? []),
        ];
    }

    /**
     * Normalize settings
     */
    protected function normalizeSettings(string $type, array $settings): array
    {
        $defaults = match ($type) {
            'hero' => [
                'height' => 'auto',
                'overlay' => false,
                'overlay_color' => 'dark',
            ],

            'features' => [
                'columns' => 3,
            ],

            default => [],
        };

        return array_merge($defaults, $settings);
    }

    /**
     * Normalize data
     */
    protected function normalizeData(string $type, array $data): array
    {
        $defaults = match ($type) {

            'hero' => [
                'title' => ['en' => ''],
                'subtitle' => ['en' => ''],
                'background_image' => null,
                'button' => [
                    'text' => ['en' => ''],
                    'url' => '#',
                ],
            ],

            default => [],
        };

        return $this->mergeRecursive($defaults, $data);
    }

    /**
     * Recursive merge (keeps nested structure safe)
     */
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