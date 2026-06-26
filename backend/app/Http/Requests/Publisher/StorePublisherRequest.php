<?php

namespace App\Http\Requests\Publisher;

use Illuminate\Foundation\Http\FormRequest;

class StorePublisherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('create publishers');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:publishers,name',
            'description' => 'nullable|string',
            'country' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ];
    }
}
