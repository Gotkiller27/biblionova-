<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepositRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        $depositRequest = $this->route('deposit_request');
        
        // Seul l'applicant peut modifier sa demande
        if ($depositRequest->applicant_id !== $this->user()->id) {
            return false;
        }

        // Modification possible uniquement si status = pending
        return $depositRequest->status === 'pending';
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'proposed_file' => ['sometimes', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères',
            'proposed_file.mimes' => 'Le fichier doit être au format PDF, DOC ou DOCX',
            'proposed_file.max' => 'Le fichier ne doit pas dépasser 10 Mo',
        ];
    }
}
