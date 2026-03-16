<?php

namespace Modules\Media\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Support\MediaConversionRegistry;

class ImageVariantService
{
    protected ImageManager $image;

    public function __construct()
    {
        $this->image = new ImageManager(new Driver());
    }

    public function generate(string $disk, string $path, ?string $modelType = null): void
    {
        $variants = MediaConversionRegistry::get($modelType);

        if (!$variants) {
            return;
        }

        $file = Storage::disk($disk)->get($path);

        $image = $this->image->read($file);

        foreach ($variants as $name => $width) {

            $variant = clone $image;

            $variant->scale(width: $width);

            $variantPath = $this->variantPath($path, $name);

            Storage::disk($disk)->put(
                $variantPath,
                $variant->encode()
            );
        }
    }

    protected function variantPath(string $path, string $variant): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $name = pathinfo($path, PATHINFO_FILENAME);

        return dirname($path) . "/{$name}-{$variant}.{$extension}";
    }
}