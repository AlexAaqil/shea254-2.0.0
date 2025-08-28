<?php

namespace App\Http\Requests\Deliveries;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AreaRequest extends FormRequest
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
            'area_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('delivery_areas', 'area_name')->ignore($this->route('delivery_area')),
            ],
            'price' => ['required', 'numeric'],
            'delivery_location_id' => ['required', 'exists:delivery_locations,id'],
        ];
    }
}
