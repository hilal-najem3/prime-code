<?php

namespace App\Http\Requests\Services\Orders;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Role;
use Illuminate\Validation\Rule;

class CreateServiceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust based on your auth logic
    }

    public function rules(): array
    {
        $userRole = Role::where('name', 'user')->first();
        $adminRole = Role::where('name', 'admin')->first();

        $users = $userRole->users()
            ->where('active', true)
            ->pluck('id')
            ->toArray();
        $admins = $adminRole->users()
            ->where('active', true)
            ->pluck('id')
            ->toArray();
        return [
            'employee_id' => ['required', 'exists:users,id', Rule::in($admins)],
            'customer_id' => ['required', 'exists:users,id', Rule::in($users)],
            'service_id' => ['required', 'exists:services,id'],
            'currency_id' => ['required', 'exists:currencies,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,processing,approved,rejected,completed'],
            'payment_status' => ['required', 'in:unpaid,paid,failed,refunded'],
            'notes' => ['nullable', 'string'],
        ];
    }
}