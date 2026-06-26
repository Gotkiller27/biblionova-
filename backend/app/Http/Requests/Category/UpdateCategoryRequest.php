<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Category;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('update categories');
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')->id;
        $category = Category::find($categoryId);

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('categories')->ignore($categoryId)],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('categories')->ignore($categoryId)],
            'description' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($categoryId, $category) {
                    if ($value) {
                        $parent = Category::find($value);
                        
                        // Prevent circular reference
                        if ($parent && $this->wouldCreateCircularReference($category, $value)) {
                            $fail('Impossible de définir cette catégorie comme parent (référence circulaire).');
                        }
                        
                        // Limit depth
                        if ($parent && $parent->depth() >= 10) {
                            $fail('La profondeur de la hiérarchie ne peut pas dépasser 10 niveaux.');
                        }
                    }
                },
            ],
            'status' => 'nullable|in:active,inactive',
        ];
    }

    private function wouldCreateCircularReference($category, $newParentId)
    {
        if (!$category) return false;
        
        $currentParent = Category::find($newParentId);
        while ($currentParent) {
            if ($currentParent->id === $category->id) {
                return true;
            }
            $currentParent = $currentParent->parent;
        }
        
        return false;
    }
}
