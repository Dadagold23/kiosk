<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'alternate_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'in:male,female,other,prefer_not_to_say'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'local_government_area' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'delivery_contact_name' => ['nullable', 'string', 'max:255'],
            'delivery_phone' => ['nullable', 'string', 'max:30'],
            'delivery_address_line_1' => ['nullable', 'string', 'max:255'],
            'delivery_address_line_2' => ['nullable', 'string', 'max:255'],
            'delivery_city' => ['nullable', 'string', 'max:120'],
            'delivery_state' => ['nullable', 'string', 'max:120'],
            'delivery_local_government_area' => ['nullable', 'string', 'max:150'],
            'delivery_postal_code' => ['nullable', 'string', 'max:30'],
            'delivery_country' => ['nullable', 'string', 'max:120'],
            'delivery_landmark' => ['nullable', 'string', 'max:255'],
            'preferred_payment_method' => ['nullable', 'string', 'in:paystack,bank_transfer,cash_deposit,wallet,card,transfer'],
            'billing_name' => ['nullable', 'string', 'max:255'],
            'billing_email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],
            'billing_phone' => ['nullable', 'string', 'max:30'],
            'billing_address' => ['nullable', 'string', 'max:1000'],
            'kyc_status' => ['nullable', 'string', 'in:not_submitted,pending,approved,rejected,requires_review'],
            'identity_type' => ['nullable', 'string', 'in:nin,national_id,drivers_license,international_passport,voters_card,residence_permit,other'],
            'identity_number' => ['nullable', 'string', 'max:120'],
            'identity_country' => ['nullable', 'string', 'max:120'],
        ];
    }
}
