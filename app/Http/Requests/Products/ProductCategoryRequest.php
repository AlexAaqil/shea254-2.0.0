<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductCategoryRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories', 'title')->ignore($this->route('product_category')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'A category title is required.',
            'title.string'   => 'The category title must be a valid string.',
            'title.max'      => 'The category title may not be greater than 255 characters.',
            'title.unique'   => 'This category title is taken. Please choose another one.',
        ];
    }
}
