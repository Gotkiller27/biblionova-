<?php

namespace App\Http\Requests\Reference;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('update references');
    }

    public function rules(): array
    {
        $referenceId = $this->route('reference')->id;
        
        return [
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'abstract' => 'nullable|string',
            'isbn' => ['nullable', 'string', 'max:20', Rule::unique('references')->ignore($referenceId)],
            'doi' => ['nullable', 'string', 'max:255', Rule::unique('references')->ignore($referenceId)],
            'issn' => ['nullable', 'string', 'max:20', Rule::unique('references')->ignore($referenceId)],
            'publication_year' => 'nullable|integer|digits:4',
            'language' => 'nullable|in:fr,en,other',
            'document_type' => 'nullable|in:livre,memoire,these,article,revue,rapport,guide,autre',
            'category_id' => 'sometimes|required|exists:categories,id',
            'publisher_id' => 'nullable|exists:publishers,id',
            'bibliothecaire_id' => 'nullable|exists:users,id',
            'cover_image' => 'nullable|image|max:5120',
            'file_path' => 'nullable|file|mimes:pdf,epub,mobi|max:51200',
            'pages' => 'nullable|integer|min:1',
            'status' => 'nullable|in:draft,published,archived',
            'visibility' => 'nullable|in:public,private,restricted',
            'availability' => 'nullable|in:available,borrowed,reserved,unavailable',
            'authors' => 'nullable|array',
            'authors.*' => 'exists:authors,id',
            'keywords' => 'nullable|array',
            'keywords.*' => 'exists:keywords,id',
        ];
    }
}
