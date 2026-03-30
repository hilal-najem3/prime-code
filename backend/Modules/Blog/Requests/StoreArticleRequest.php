<?php

namespace Modules\Blog\Requests;

use Illuminate\Foundation\Http\FormRequest;

/*
|--------------------------------------------------------------------------
| Store Article Request
|--------------------------------------------------------------------------
|
| Handles validation for creating articles.
| Ensures multilingual structure and media integrity.
|
*/

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Multilingual Fields
            |--------------------------------------------------------------------------
            */

            'title' => ['required', 'array'],
            'title.*' => ['required', 'string', 'max:191'],

            'content' => ['required', 'array'],
            'content.*' => ['required'], // HTML or blocks later

            'excerpt' => ['nullable', 'array'],
            'excerpt.*' => ['nullable', 'string'],

            /*
            |--------------------------------------------------------------------------
            | Slug (optional, auto-generated if missing)
            |--------------------------------------------------------------------------
            */

            'slug' => ['nullable', 'array'],
            'slug.*' => ['nullable', 'string', 'max:191'],

            /*
            |--------------------------------------------------------------------------
            | Publishing
            |--------------------------------------------------------------------------
            */

            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],

            /*
            |--------------------------------------------------------------------------
            | Media
            |--------------------------------------------------------------------------
            */

            'featured_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'gallery_ids' => ['nullable', 'array'],
            'gallery_ids.*' => ['integer', 'exists:media,id'],

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            'meta_title' => ['nullable', 'array'],
            'meta_title.*' => ['nullable', 'string', 'max:191'],

            'meta_description' => ['nullable', 'array'],
            'meta_description.*' => ['nullable', 'string'],
        ];
    }
}
