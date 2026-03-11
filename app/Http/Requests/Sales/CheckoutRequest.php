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
        $rules = [
            'full_name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:200'],

            'delivery_method' => ['required', Rule::in(['delivery','shop'])],
            'location' => ['required_if:delivery_method,delivery', 'nullable', 'exists:delivery_locations,id'],
            'area' => ['required_if:delivery_method,delivery', 'nullable', 'exists:delivery_areas,id'],
            'address' => ['required_if:delivery_method,delivery', 'nullable', 'string', 'max:255'],
            
            'additional_information' => ['nullable', 'string', 'max:255'],

            'payment_method' => ['required', Rule::in(['kcb_mpesa', 'paystack', 'paypal'])],
        ];

        if ($this->input('payment_method') === 'kcb_mpesa') {
            $rules['phone_number'] = [
                'required',
                'regex:/^254(7|1)\d{8}$/',
                'size:12'
            ];
        } else {
            $rules['phone_number'] = [
                'required',
                'string',
                'min:7',
                'max:15',
                'regex:/^\+?[0-9\s\-\(\)]+$/'
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Please enter your full name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            
            'phone_number.required' => 'Phone number is required.',
            'phone_number.regex' => $this->input('payment_method') === 'kcb_mpesa' 
                ? 'For M-Pesa, use format 2547XXXXXXXX or 2541XXXXXXXX (e.g., 254712345678).' 
                : 'Please enter a valid phone number with country code (e.g., +1234567890).',
            'phone_number.size' => 'M-Pesa number must be exactly 12 digits (254 followed by 9 digits).',
            'phone_number.min' => 'Phone number must be at least :min characters.',
            'phone_number.max' => 'Phone number must not exceed :max characters.',
            
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected. Choose MPesa, Paystack or PayPal',
            
            'delivery_method.required' => 'Please select a delivery method.',
            'location.required_if' => 'Please select a delivery location.',
            'area.required_if' => 'Please select a delivery area.',
            'address.required_if' => 'Please provide a delivery address.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $input = $this->all();

        // Handle phone number formatting based on payment method
        if ($this->has('phone_number') && $this->has('payment_method')) {
            $phoneNumber = $this->input('phone_number');
            $paymentMethod = $this->input('payment_method');
            
            // Remove all whitespace
            $phoneNumber = preg_replace('/\s+/', '', $phoneNumber);
            
            if ($paymentMethod === 'kcb_mpesa') {
                // For M-Pesa: Convert common Kenyan formats to the required 254 format
                
                // Remove any non-numeric characters
                $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);
                
                // If it starts with 0 (e.g., 0712345678 -> 254712345678)
                if (strlen($cleaned) === 10 && substr($cleaned, 0, 1) === '0') {
                    $cleaned = '254' . substr($cleaned, 1);
                }
                
                // If it starts with 7 or 1 without country code (e.g., 712345678 -> 254712345678)
                if (strlen($cleaned) === 9 && (substr($cleaned, 0, 1) === '7' || substr($cleaned, 0, 1) === '1')) {
                    $cleaned = '254' . $cleaned;
                }
                
                $input['phone_number'] = $cleaned;
            } else {
                // For Paystack: Ensure it has a + if it looks like an international number without one
                if (substr($phoneNumber, 0, 1) !== '+' && preg_match('/^\d{10,}$/', $phoneNumber)) {
                    // If it's all digits and longer than 10, it's probably international without +
                    $input['phone_number'] = '+' . $phoneNumber;
                } else {
                    $input['phone_number'] = $phoneNumber;
                }
            }
        }

        $this->replace($input);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional validation logic if needed
            $paymentMethod = $this->input('payment_method');
            $phoneNumber = $this->input('phone_number');

            if ($paymentMethod === 'kcb_mpesa') {
                // Additional check: Ensure Safaricom numbers start with 7 or 1
                // This is already covered by regex, but adding a clearer message
                if ($phoneNumber && !preg_match('/^254[71]/', $phoneNumber)) {
                    $validator->errors()->add(
                        'phone_number', 
                        'M-Pesa requires a Safaricom number starting with 07 or 01.'
                    );
                }
            }
        });
    }

}
