<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRevisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('bibliothecaire');
    }

    public function rules(): array
    {
        return [
            'reference_id' => ['required', 'exists:references,id'],
            'action' => ['required', Rule::in(['creation', 'modification', 'correction', 'archivage'])],
            'commentaire' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'reference_id.required' => 'L\'identifiant de la référence est obligatoire',
            'reference_id.exists' => 'La référence spécifiée n\'existe pas',
            'action.required' => 'L\'action est obligatoire',
            'action.in' => 'L\'action doit être creation, modification, correction ou archivage',
        ];
    }
}
