<?php

namespace App\Http\Requests\Keyword;

use Illuminate\Foundation\Http\FormRequest;

class StoreKeywordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('create keywords');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:keywords,name',
            'slug' => 'nullable|string|max:255|unique:keywords,slug',
            'description' => 'nullable|string',
            'usage_count' => 'nullable|integer|min:0',
            'popularity_score' => 'nullable|integer|min:0',
        ];
    }
}
