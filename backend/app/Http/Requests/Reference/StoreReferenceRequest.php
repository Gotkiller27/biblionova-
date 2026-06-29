<?php

namespace App\Http\Requests\Reference;

use Illuminate\Foundation\Http\FormRequest;

class StoreReferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('create references');
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'abstract' => 'nullable|string',
            'isbn' => 'nullable|string|max:20|unique:references,isbn',
            'doi' => 'nullable|string|max:255|unique:references,doi',
            'issn' => 'nullable|string|max:20|unique:references,issn',
            'publication_year' => 'nullable|integer|digits:4',
            'language' => 'nullable|in:fr,en,other',
            'document_type' => 'nullable|in:livre,memoire,these,article,revue,rapport,guide,autre',
            'category_id' => 'required|exists:categories,id',
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
