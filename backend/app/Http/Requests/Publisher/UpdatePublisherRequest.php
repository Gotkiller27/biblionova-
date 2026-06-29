<?php

namespace App\Http\Requests\Publisher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePublisherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('update publishers');
    }

    public function rules(): array
    {
        $publisherId = $this->route('publisher')->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('publishers')->ignore($publisherId)],
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
        ];
    }
}
