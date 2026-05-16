<?php

namespace Modules\Blog\Requests;

use Illuminate\Foundation\Http\FormRequest;

/*
|--------------------------------------------------------------------------
| Update Article Request
|--------------------------------------------------------------------------
|
| Handles validation for updating articles.
| Allows partial updates.
|
*/

class UpdateArticleRequest extends FormRequest
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
            | Multilingual Fields
            |--------------------------------------------------------------------------
            */

            'title' => ['sometimes', 'array'],
            'title.*' => ['required_with:title', 'string', 'max:191'],

            'content' => ['sometimes', 'array'],
            'content.*' => ['required_with:content'],

            'excerpt' => ['nullable', 'array'],
            'excerpt.*' => ['nullable', 'string'],

            /*
            |--------------------------------------------------------------------------
            | Slug
            |--------------------------------------------------------------------------
            */

            'slug' => ['nullable', 'array'],
            'slug.*' => ['nullable', 'string', 'max:191'],

            /*
            |--------------------------------------------------------------------------
            | Publishing
            |--------------------------------------------------------------------------
            */

            'status' => ['sometimes', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],

            /*
            |--------------------------------------------------------------------------
            | Media
            |--------------------------------------------------------------------------
            */

            'featured_media_id' => ['nullable', 'uuid', 'exists:media,id'],
            'gallery_ids' => ['nullable', 'array'],
            'gallery_ids.*' => ['uuid', 'exists:media,id'],

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
