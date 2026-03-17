<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Example usage in blade
        @php
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

        // Clean the number - remove all non-numeric except +
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // Remove duplicate plus signs
        if (substr_count($cleaned, '+') > 1) {
            $cleaned = '+' . preg_replace('/\+/', '', $cleaned);
        }
        
        // Convert 00 to +
        if (strpos($cleaned, '00') === 0) {
            $cleaned = '+' . substr($cleaned, 2);
        }

        // ===== KENYAN NUMBER FORMATS =====
        
        // Case 1: Already has +254 (with or without formatting)
        if (preg_match('/^\+?254(\d{9})$/', $cleaned, $matches)) {
            return '+254 ' . substr($matches[1], 0, 3) . ' ' . substr($matches[1], 3, 3) . ' ' . substr($matches[1], 6);
        }
        
        // Case 2: Starts with 07 (Safaricom/Airtel) - 10 digits total
        if (preg_match('/^07(\d{8})$/', $cleaned, $matches)) {
            return '+254 7' . substr($matches[1], 0, 2) . ' ' . substr($matches[1], 2, 3) . ' ' . substr($matches[1], 5);
        }
        
        // Case 3: Starts with 01 (Telkom) - 10 digits total
        if (preg_match('/^01(\d{8})$/', $cleaned, $matches)) {
            return '+254 1' . substr($matches[1], 0, 2) . ' ' . substr($matches[1], 2, 3) . ' ' . substr($matches[1], 5);
        }
        
        // Case 4: Starts with 7 (already has 254 but missing the +)
        if (preg_match('/^7(\d{8})$/', $cleaned, $matches)) {
            return '+254 7' . substr($matches[1], 0, 2) . ' ' . substr($matches[1], 2, 3) . ' ' . substr($matches[1], 5);
        }
        
        // Case 5: Starts with 1 (Telkom, missing 254)
        if (preg_match('/^1(\d{8})$/', $cleaned, $matches)) {
            return '+254 1' . substr($matches[1], 0, 2) . ' ' . substr($matches[1], 2, 3) . ' ' . substr($matches[1], 5);
        }
        
        // Case 6: 10-digit number starting with 0 (generic)
        if (preg_match('/^0(\d{9})$/', $cleaned, $matches)) {
            $firstDigit = substr($matches[1], 0, 1);
            if ($firstDigit === '7' || $firstDigit === '1') {
                return '+254 ' . $firstDigit . substr($matches[1], 1, 2) . ' ' . substr($matches[1], 3, 3) . ' ' . substr($matches[1], 6);
            }
        }

        // ===== INTERNATIONAL FORMATS =====
        
        // Format with spaces every 3 digits after country code
        if (strpos($cleaned, '+') === 0) {
            // Extract country code (up to 3 digits)
            preg_match('/^\+(\d{1,3})/', $cleaned, $codeMatches);
            $countryCode = $codeMatches[1] ?? '';
            $countryCodeLen = strlen($countryCode);
            
            $rest = substr($cleaned, $countryCodeLen + 1); // +1 for the plus sign
            
            // Format the rest in groups of 3
            $rest = preg_replace('/(\d{3})(?=\d)/', '$1 ', $rest);
            
            return '+' . $countryCode . ' ' . $rest;
        }
        
        // If all else fails, return cleaned number
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
                '233' => 'Ghana',
                '20' => 'Egypt',
                '212' => 'Morocco',
                '27' => 'South Africa',
            ];
            
            return $countries[$code] ?? 'International';
        }
        
        // Check for Kenyan numbers without +254
        if (preg_match('/^(0?7|0?1|2547|2541)/', $cleaned)) {
            return 'Kenya';
        }
        
        // Check for other common patterns
        if (preg_match('/^(00254|0254)/', $cleaned)) {
            return 'Kenya';
        }
        
        return 'Unknown';
    }

    /**
     * Convert any Kenyan number to +254 format
     */
    public static function toInternationalFormat($phoneNumber): string
    {
        if (empty($phoneNumber)) {
            return '';
        }

        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // If already in international format, return as is
        if (preg_match('/^\+254\d{9}$/', $cleaned)) {
            return $cleaned;
        }
        
        // Remove leading 0 if present
        $cleaned = ltrim($cleaned, '0');
        
        // If it starts with 254, add + if missing
        if (preg_match('/^254(\d{9})$/', $cleaned, $matches)) {
            return '+' . $cleaned;
        }
        
        // If it's a 9-digit number starting with 7 or 1
        if (preg_match('/^([71])(\d{8})$/', $cleaned, $matches)) {
            return '+254' . $matches[1] . $matches[2];
        }
        
        // If it's a 10-digit number starting with 07 or 01
        if (preg_match('/^0([71])(\d{8})$/', $cleaned, $matches)) {
            return '+254' . $matches[1] . $matches[2];
        }
        
        return $cleaned;
    }
}