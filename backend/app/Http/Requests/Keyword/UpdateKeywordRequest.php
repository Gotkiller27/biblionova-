<?php

namespace App\Http\Requests\Keyword;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKeywordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('update keywords');
    }

    public function rules(): array
    {
        $keywordId = $this->route('keyword')->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('keywords')->ignore($keywordId)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('keywords')->ignore($keywordId)],
            'description' => 'nullable|string',
            'usage_count' => 'nullable|integer|min:0',
            'popularity_score' => 'nullable|integer|min:0',
        ];
    }
}
