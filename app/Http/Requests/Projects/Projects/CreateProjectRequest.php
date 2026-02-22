<?php

namespace App\Http\Requests\Projects\Projects;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class CreateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $languages = Language::where('is_active', true)->pluck('code')->toArray();

        $titleRules = $contentRules = [];
        foreach ($languages as $lang) {
            $titleRules["title.$lang"] = ['required', 'string', 'max:255'];
            $contentRules["content.$lang"] = ['required', 'string'];
        }

        return array_merge([
            'link' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:posts,link'],
            'thumbnail' => ['required', 'image', 'max:2048'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['nullable', 'array'],
            'gallery.*.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:2048'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:post_categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:post_tags,id'],
            'links' => ['nullable', 'array'],
            'links.*' => ['nullable', 'url', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'is_featured' => ['required', 'boolean'],
        ], $titleRules, $contentRules);
    }

    public function messages(): array
    {
        return [
            'link.unique' => 'The link (slug) has already been taken.',
            'thumbnail.required' => 'A thumbnail image is required.',
            'title.*.required' => 'The title is required for each language.',
            'content.*.required' => 'The content is required for each language.',
        ];
    }
}