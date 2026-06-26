<?php

namespace App\Http\Requests\DepositRequest;

use Illuminate\Foundation\Http\FormRequest;

class ReviewDepositRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'responsable_validation']);
    }

    public function rules(): array
    {
        return [
            'decision' => 'required|in:approve,reject,second_review',
            'justification' => 'nullable|string',
        ];
    }
}
