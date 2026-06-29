<?php

namespace App\Http\Requests\DepositRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|max:51200',
            'file_type' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
        ];
    }
}
