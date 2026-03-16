<?php

namespace Modules\Media\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
                'integer',
                'exists:media,id'
            ],

            'model_type' => [
                'required',
                'string'
            ],

            'model_id' => [
                'required',
                'integer'
            ],

            'collection' => [
                'nullable',
                'string'
            ]
        ];
    }
}