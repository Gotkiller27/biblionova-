<?php

namespace App\Http\Requests\Citation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'context' => 'nullable|string|max:2000',
            'citation_style' => 'nullable|in:apa,mla,chicago,harvard,vancouver,ieee',
            'page_number' => 'nullable|integer|min:1',
        ];
    }
}
