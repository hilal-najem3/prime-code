<?php

namespace Modules\Subscriptions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignPlanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'plan_id' => ['required', 'uuid', 'exists:plans,id'],
        ];
    }
}
