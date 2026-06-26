<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class AssignPermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('assign permissions');
    }

    public function rules(): array
    {
        return [
            'permission' => 'required|exists:permissions,name',
        ];
    }
}
