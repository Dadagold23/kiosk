<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'delivery_address' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'accept_terms' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return [
            'delivery_address' => 'delivery address',
            'accept_terms' => 'terms and conditions',
        ];
    }
}
