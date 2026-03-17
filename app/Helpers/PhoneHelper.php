<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Example usage in blade
        @php
            use App\Helpers\PhoneHelper;
            $formattedPhone = PhoneHelper::formatForDisplay($order->order_delivery->phone_number);
            $phoneCountry = PhoneHelper::getCountry($order->order_delivery->phone_number);
        @endphp

        <p>
            <span>Phone Number</span>
            <span>
                {{ $formattedPhone }}
                @if($phoneCountry !== 'Kenya')
                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $phoneCountry }}
                    </span>
                @endif
            </span>
        </p>
     */
    /**
     * Format phone number for display
     */
    public static function formatForDisplay($phoneNumber): string
    {
        if (empty($phoneNumber)) {
            return '';
        }

        // Clean the number
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // Remove duplicate plus signs
        if (substr_count($cleaned, '+') > 1) {
            $cleaned = '+' . preg_replace('/\+/', '', $cleaned);
        }
        
        // Convert 00 to +
        if (strpos($cleaned, '00') === 0) {
            $cleaned = '+' . substr($cleaned, 2);
        }

        // Kenya numbers (254)
        if (preg_match('/^\+?254(\d{9})$/', $cleaned, $matches)) {
            return '+254 ' . substr($matches[1], 0, 3) . ' ' . substr($matches[1], 3, 3) . ' ' . substr($matches[1], 6);
        }
        
        // Local Kenyan format (07xx xxx xxx)
        if (preg_match('/^0(\d{9})$/', $cleaned, $matches)) {
            return '0' . substr($matches[1], 0, 3) . ' ' . substr($matches[1], 3, 3) . ' ' . substr($matches[1], 6);
        }
        
        // International format - preserve as is
        if (strpos($cleaned, '+') === 0) {
            // Format with spaces every 3 digits after country code
            $countryCode = substr($cleaned, 0, 4); // +XXX
            $rest = substr($cleaned, 4);
            $rest = preg_replace('/(\d{3})(?=\d)/', '$1 ', $rest);
            return $countryCode . ' ' . $rest;
        }
        
        return $cleaned;
    }

    /**
     * Get country name from phone number
     */
    public static function getCountry($phoneNumber): string
    {
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // Extract country code
        if (preg_match('/^\+(\d{1,3})/', $cleaned, $matches)) {
            $code = $matches[1];
            
            $countries = [
                '254' => 'Kenya',
                '255' => 'Tanzania',
                '256' => 'Uganda',
                '250' => 'Rwanda',
                '257' => 'Burundi',
                '1' => 'US/Canada',
                '44' => 'UK',
                '91' => 'India',
                '234' => 'Nigeria',
                '27' => 'South Africa',
            ];
            
            return $countries[$code] ?? 'International';
        }
        
        // Default to Kenya if starts with 0 or 254
        if (strpos($cleaned, '0') === 0 || strpos($cleaned, '254') === 0) {
            return 'Kenya';
        }
        
        return 'Unknown';
    }
}