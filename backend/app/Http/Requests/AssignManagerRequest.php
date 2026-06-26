<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignManagerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'manager_id' => ['required', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'manager_id.required' => 'L\'identifiant du responsable est obligatoire',
            'manager_id.exists' => 'Le responsable spécifié n\'existe pas',
        ];
    }
}
