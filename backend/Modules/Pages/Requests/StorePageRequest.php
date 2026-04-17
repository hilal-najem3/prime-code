<?php

namespace Modules\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;

/*
|--------------------------------------------------------------------------
| Store Page Request
|--------------------------------------------------------------------------
|
| Handles validation for creating pages.
|
*/

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // handled by middleware
    }

    public function rules(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | Core Content
            |--------------------------------------------------------------------------
            */

            'title' => 'required|array|min:1',
            'title.*' => 'required|string|max:191',

            'slug' => 'required|array|min:1',
            'slug.*' => 'required|string|max:191|regex:/^[a-z0-9\-]+$/',

            'content' => 'nullable|array',
            'content.*.type' => 'required|string|max:100',
            'content.*.variant' => 'nullable|string|max:100',
            'content.*.settings' => 'nullable|array',
            'content.*.data' => 'required|array',

            'layout' => 'nullable|string|max:191',

            /*
            |--------------------------------------------------------------------------
            | State
            |--------------------------------------------------------------------------
            */

            'status' => 'required|in:draft,published',
            'is_homepage' => 'nullable|boolean',

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            'meta_title' => 'nullable|array',
            'meta_title.*' => 'nullable|string|max:191',

            'meta_description' => 'nullable|array',
            'meta_description.*' => 'nullable|string|max:500',
        ];
    }
}