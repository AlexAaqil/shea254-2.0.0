<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductMeasurementRequest extends FormRequest
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
            'measurement_name' => [
                'required',
                'string',
                'max:40',
                Rule::unique('product_measurements', 'measurement_name')->ignore($this->route('product_measurement')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'measurement_name.required' => 'Measurement name is required.',
            'measurement_name.string'   => 'Measurement name must be a valid string.',
            'measurement_name.max'      => 'Measurement name may not be greater than 255 characters.',
            'measurement_name.unique'   => 'Measurement name is taken. Please choose another one.',
        ];
    }
}
