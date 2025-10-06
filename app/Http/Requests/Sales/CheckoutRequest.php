<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
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
            'full_name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:200'],
            'phone_number' => [
                'required',
                'regex:/^254(7|1)\d{8}$/',
            ],
            'delivery_method' => ['required', Rule::in(['delivery','shop'])],

            'location' => ['required_if:delivery_method,delivery', 'nullable', 'exists:delivery_locations,id'],
            'area' => ['required_if:delivery_method,delivery', 'nullable', 'exists:delivery_areas,id'],
            'address' => ['required_if:delivery_method,delivery', 'nullable', 'string', 'max:255'],
            'additional_information' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.regex' => 'Use format 2547xxxxxxxx or 2541xxxxxxxx.',
            'location.required_if' => 'Please select a delivery location.',
            'area.required_if' => 'Please select a delivery area.',
            'address.required_if' => 'Please provide a delivery address.',
        ];
    }
}
