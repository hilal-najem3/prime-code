<?php

namespace Modules\Pages\Blocks\Contracts;

interface BlockInterface
{
    public function type(): string;

    public function normalizeSettings(array $settings): array;

    public function normalizeData(array $data): array;
}