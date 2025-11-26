<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
            'title'=> [
                'required',
                'string',
                'max:120',
                Rule::unique('products', 'title')->ignore($this->route('product')),
            ],
            'product_code' => 'nullable|numeric',
            'is_visible' => ['sometimes', 'boolean'],
            'featured' => ['sometimes', 'boolean'],
            'buying_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'stock_count' => ['nullable', 'integer', 'min:0'],
            'safety_stock' => ['nullable', 'integer', 'min:0'],
            'product_measurement' => ['nullable', 'numeric', 'min:0'],
            'product_order' => ['nullable', 'integer', 'min:0'],
            
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',

            'category_id' => ['nullable', 'exists:product_categories,id'],
            'measurement_id' => ['nullable', 'exists:product_measurements,id'],

            'price_tiers' => 'nullable|array',
            'price_tiers.*.min_quantity' => 'required_with:price_tiers.*.price|integer|min:2',
            'price_tiers.*.price' => 'required_with:price_tiers.*.min_quantity|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The product title is required.',
            'title.unique' => 'A product with this title already exists.',

            'images.*.image' => 'Each file must be a valid image.',
            'images.*.max' => 'Each image must be under 2MB.',
            'images.max' => 'You can upload a maximum of 5 images.',
        ];
    }
}
