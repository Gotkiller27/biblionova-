<?php

namespace App\Http\Requests\Citation;

use Illuminate\Foundation\Http\FormRequest;

class CiteReferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('manage citations');
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
