<?php

namespace Modules\Media\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'filename'   => $this->filename,
            'extension'  => $this->extension,
            'mime_type'  => $this->mime_type,
            'size'       => $this->size,
            'alt_text'   => $this->alt_text,
            'collection' => $this->collection,

            'model_type' => $this->model_type,
            'model_id'   => $this->model_id,

            'url'        => $this->url,

            // 🔥 NEW
            'variants'   => $this->variants,

            'created_at' => $this->created_at,
        ];
    }
}