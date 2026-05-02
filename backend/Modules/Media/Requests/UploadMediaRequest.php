<?php

namespace Modules\Media\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Uploaded File
            |--------------------------------------------------------------------------
            */

            'file' => [
                'required',
                'file',
                'max:20480' // 20MB
            ],

            /*
            |--------------------------------------------------------------------------
            | Storage Directory
            |--------------------------------------------------------------------------
            */

            'directory' => [
                'required',
                'string',
                'max:100'
            ],

            /*
            |--------------------------------------------------------------------------
            | Media Collection
            |--------------------------------------------------------------------------
            */

            'collection' => [
                'nullable',
                'string',
                'max:100'
            ],

            /*
            |--------------------------------------------------------------------------
            | Polymorphic Attachment
            |--------------------------------------------------------------------------
            */

            'model_type' => [
                'nullable',
                'string'
            ],

            'model_id' => [
                'nullable',
                'integer'
            ],

            /*
            |--------------------------------------------------------------------------
            | Optional Alt Text
            |--------------------------------------------------------------------------
            */

            'alt_text' => [
                'nullable',
                'string',
                'max:191'
            ],

            'disk' => ['nullable', 'in:public,private'],
        ];
    }
}