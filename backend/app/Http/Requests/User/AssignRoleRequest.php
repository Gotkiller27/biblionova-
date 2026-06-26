<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class AssignRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('assign roles');
    }

    public function rules(): array
    {
        return [
            'role' => 'required|exists:roles,name',
        ];
    }
}
