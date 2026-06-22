<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'booking_type' => ['required', 'string', 'max:50'],
            'title' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'check_in_date' => ['nullable', 'date'],
            'check_out_date' => ['nullable', 'date', 'after_or_equal:check_in_date'],
            'travel_date' => ['nullable', 'date'],
            'persons' => ['required', 'integer', 'min:1', 'max:50'],
            'details' => ['nullable', 'string', 'max:4000'],
            'payment_method' => ['required', 'string', 'max:50'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']
        ];
    }
    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();
    }

}
