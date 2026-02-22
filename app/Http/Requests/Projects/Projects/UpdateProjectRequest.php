<?php

namespace App\Http\Requests\Projects\Projects;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Language;
use App\Models\Project;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $projectId = $this->route('project'); // This assumes route model binding or {project} param
        $languages = Language::where('is_active', true)->pluck('code')->toArray();

        $titleRules = $contentRules = [];
        foreach ($languages as $lang) {
            $titleRules["title.$lang"] = ['required', 'string', 'max:255'];
            $contentRules["content.$lang"] = ['required', 'string'];
        }

        $project = Project::findorFail($projectId);
        $mediaIds = $project->media->where('id', '!=', $project->thumbnail_id)->pluck('id')->toArray();

        return array_merge([
            'link' => ['required', 'string', 'max:255', 'alpha_dash', "unique:projects,link,{$projectId}"],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['nullable', 'array'],
            'gallery.*.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:2048'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:project_categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:project_tags,id'],
            'links' => ['nullable', 'array'],
            'links.*' => ['nullable', 'url', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'is_featured' => ['required', 'boolean'],
            'deleted_media' => ['nullable', 'string', function ($attribute, $value, $fail) use ($mediaIds) {
                $ids = array_filter(explode(',', $value), fn($id) => trim($id) !== '');
                foreach ($ids as $id) {
                    if (!in_array($id, $mediaIds)) {
                        return $fail("The selected media ID [$id] is invalid or not deletable.");
                    }
                }
            },],
        ], $titleRules, $contentRules);
    }

    public function messages(): array
    {
        return [
            'link.unique' => 'The link (slug) has already been taken.',
            'title.*.required' => 'The title is required for each language.',
            'content.*.required' => 'The content is required for each language.',
        ];
    }
}