<?php

namespace App\Http\Requests\DepositRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepositRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'authors' => 'nullable|string',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|digits:4',
            'language' => 'nullable|in:fr,en,other',
            'file_path' => 'nullable|file|mimes:pdf,epub,mobi|max:51200',
            'assigned_manager_id' => 'nullable|exists:users,id',
        ];
    }
}
