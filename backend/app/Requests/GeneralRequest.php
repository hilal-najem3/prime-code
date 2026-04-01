<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'search' => ['nullable', 'string', 'max:191'],
            'sort' => [
                'nullable',
                'string',
                'in:id,name,slug,domain,status,created_at'
            ],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
