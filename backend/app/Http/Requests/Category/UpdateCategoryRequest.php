<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('update categories');
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')->id;
        
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('categories')->ignore($categoryId)],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('categories')->ignore($categoryId)],
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
