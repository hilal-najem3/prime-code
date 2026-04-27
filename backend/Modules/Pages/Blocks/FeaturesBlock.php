<?php

namespace Modules\Pages\Blocks;

use Modules\Pages\Blocks\Contracts\BlockInterface;

class FeaturesBlock implements BlockInterface
{
    public function type(): string
    {
        return 'features';
    }

    public function normalizeSettings(array $settings): array
    {
        return array_merge([
            'columns' => 3,
        ], $settings);
    }

    public function normalizeData(array $data): array
    {
        return $this->mergeRecursive([
            'title' => ['en' => ''],
            'items' => [],
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