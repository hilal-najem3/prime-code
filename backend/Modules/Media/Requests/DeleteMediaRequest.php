<?php

namespace Modules\Media\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMediaRequest extends FormRequest
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
            ]
        ];
    }
}
