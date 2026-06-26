<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepositRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Tout utilisateur authentifié peut créer une demande
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'proposed_file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'], // 10MB max
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères',
            'proposed_file.required' => 'Le fichier est obligatoire',
            'proposed_file.mimes' => 'Le fichier doit être au format PDF, DOC ou DOCX',
            'proposed_file.max' => 'Le fichier ne doit pas dépasser 10 Mo',
        ];
    }
}
