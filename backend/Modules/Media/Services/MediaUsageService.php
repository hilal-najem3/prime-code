<?php

namespace Modules\Media\Services;

use Modules\Media\Models\Media;

class MediaUsageService
{
    public function getUsage(Media $media)
    {
        return Media::where('disk', $media->disk)
            ->where('path', $media->path)
            ->get()
            ->map(function ($item) {

                return [
                    'media_id'   => $item->id,
                    'model_type' => $item->model_type,
                    'model_id'   => $item->model_id,
                    'collection' => $item->collection,
                ];
            });
    }
}