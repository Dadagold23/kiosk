<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmergencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_code' => ['required', 'string', 'size:2'],
            'country_name' => ['required', 'string', 'max:120'],
            'emergency_type' => ['required', 'string', 'in:' . implode(',', config('kiosk.emergency.types', []))],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'alternate_phone' => ['nullable', 'string', 'max:30'],
            'state_code' => ['nullable', 'string', 'max:50'],
            'state_name' => ['required', 'string', 'max:120'],
            'local_government_area' => ['required', 'string', 'max:150'],
            'location_text' => ['required', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'description' => ['required', 'string', 'max:3000'],
        ];
    }
}
