<?php

namespace Modules\Media\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\MediaAllowedModels;

class AttachMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'media_id' => [
                'required',
                'uuid',
                'exists:media,id'
            ],

            'model_type' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {

                    if (!MediaAllowedModels::contains($value)) {
                        $fail('Invalid model type.');
                    }
                },
            ],

            'model_id' => [
                'required',
                'uuid'
            ],

            'collection' => [
                'nullable',
                'string'
            ]
        ];
    }
}
