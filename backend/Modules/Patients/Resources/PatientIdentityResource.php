<?php

namespace Modules\Patients\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Media\Resources\MediaResource;

class PatientIdentityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'type' => $this->type,
            'number' => $this->number,

            'issued_at' => $this->issued_at?->format('Y-m-d'),
            'expires_at' => $this->expires_at?->format('Y-m-d'),

            'notes' => $this->notes,

            'media' => MediaResource::collection(
                $this->whenLoaded('media')
            ),

            'created_at' => $this->created_at,
        ];
    }
}