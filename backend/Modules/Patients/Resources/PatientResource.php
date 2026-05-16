<?php

namespace Modules\Patients\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Media\Resources\MediaResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'user_id' => $this->user_id,

            'first_name' => $this->first_name,
            'last_name' => $this->last_name,

            'full_name' => $this->full_name,

            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),

            'phone' => $this->phone,
            'phone_secondary' => $this->phone_secondary,

            'email' => $this->email,

            'address' => $this->address,

            'blood_type' => $this->blood_type,
            'allergies' => $this->allergies,

            'status' => $this->status,

            'allow_login' => $this->allow_login,

            'notes' => $this->notes,

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user?->id,
                'email' => $this->user?->email,
            ]),

            'identities' => PatientIdentityResource::collection(
                $this->whenLoaded('identities')
            ),

            'media' => MediaResource::collection(
                $this->whenLoaded('media')
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
