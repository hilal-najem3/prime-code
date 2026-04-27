<?php

namespace Modules\Pages\Services;

use Modules\Pages\Blocks\HeroBlock;
use Modules\Pages\Blocks\FeaturesBlock;
use Modules\Pages\Blocks\Contracts\BlockInterface;

class BlockRegistry
{
    protected array $blocks = [];

    public function __construct()
    {
        $this->register(new HeroBlock());
        $this->register(new FeaturesBlock());
    }

    public function register(BlockInterface $block): void
    {
        $this->blocks[$block->type()] = $block;
    }

    public function get(string $type): ?BlockInterface
    {
        return $this->blocks[$type] ?? null;
    }
}