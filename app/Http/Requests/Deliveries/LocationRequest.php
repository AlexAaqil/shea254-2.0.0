<?php

namespace App\Http\Requests\Deliveries;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'location_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('delivery_locations', 'location_name')->ignore($this->route('delivery_location')),
            ],
        ];
    }
}
