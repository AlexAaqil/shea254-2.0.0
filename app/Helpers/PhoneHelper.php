<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Format phone number for display (with spaces)
     */
    public static function formatForDisplay($phoneNumber): string
    {
        if (empty($phoneNumber)) {
            return '';
        }

        // Clean the number - remove any existing spaces first
        $cleaned = preg_replace('/\s+/', '', $phoneNumber);
        
        // Handle +074... pattern (incorrectly stored Kenyan number)
        if (preg_match('/^\+074(\d{7})$/', $cleaned, $matches)) {
            // Convert +074XXXXXXX to +254 7XX XXX XXX
            $number = '7' . $matches[1]; // Add the 7 back
            return '+254 ' . substr($number, 0, 3) . ' ' . substr($number, 3, 3) . ' ' . substr($number, 6, 3);
        }
        
        // Handle +254... pattern (correctly stored)
        if (preg_match('/^\+254(\d{9})$/', $cleaned, $matches)) {
            return '+254 ' . substr($matches[1], 0, 3) . ' ' . substr($matches[1], 3, 3) . ' ' . substr($matches[1], 6, 3);
        }
        
        // Handle 254... pattern (without plus)
        if (preg_match('/^254(\d{9})$/', $cleaned, $matches)) {
            return '+254 ' . substr($matches[1], 0, 3) . ' ' . substr($matches[1], 3, 3) . ' ' . substr($matches[1], 6, 3);
        }
        
        // Handle 07... or 01... pattern (local format)
        if (preg_match('/^0([71])(\d{8})$/', $cleaned, $matches)) {
            return '+254 ' . $matches[1] . substr($matches[2], 0, 2) . ' ' . substr($matches[2], 2, 3) . ' ' . substr($matches[2], 5, 3);
        }
        
        // Handle 7... or 1... pattern (without leading 0)
        if (preg_match('/^([71])(\d{8})$/', $cleaned, $matches)) {
            return '+254 ' . $matches[1] . substr($matches[2], 0, 2) . ' ' . substr($matches[2], 2, 3) . ' ' . substr($matches[2], 5, 3);
        }
        
        // If it's an international number with plus
        if (strpos($cleaned, '+') === 0) {
            return self::formatInternationalNumber($cleaned);
        }
        
        // Return as is if no pattern matches
        return $cleaned;
    }

    /**
     * Get country name from stored phone number
     */
    public static function getCountry($phoneNumber): string
    {
        $cleaned = preg_replace('/\s+/', '', $phoneNumber);
        
        // Fix for +074... pattern - this is actually Kenya
        if (preg_match('/^\+074/', $cleaned)) {
            return 'Kenya';
        }
        
        // Check for +254 pattern
        if (preg_match('/^\+254/', $cleaned)) {
            return 'Kenya';
        }
        
        // Check for 254 pattern (without plus)
        if (preg_match('/^254/', $cleaned)) {
            return 'Kenya';
        }
        
        // Check for 07 or 01 pattern
        if (preg_match('/^0[71]/', $cleaned)) {
            return 'Kenya';
        }
        
        // Check for 7 or 1 pattern (9 digits)
        if (preg_match('/^[71]\d{8}$/', $cleaned)) {
            return 'Kenya';
        }
        
        // Extract country code from international format
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
        
        return 'Unknown';
    }

    /**
     * Format international numbers
     */
    private static function formatInternationalNumber($number): string
    {
        // Extract country code
        preg_match('/^\+(\d{1,3})/', $number, $matches);
        $countryCode = $matches[1] ?? '';
        $rest = substr($number, strlen($countryCode) + 1); // +1 for the plus sign
        
        // Format based on common patterns
        if ($countryCode === '1') { // US/Canada
            if (strlen($rest) === 10) {
                return '+1 ' . substr($rest, 0, 3) . ' ' . substr($rest, 3, 3) . ' ' . substr($rest, 6, 4);
            }
        } elseif ($countryCode === '44') { // UK
            if (strlen($rest) === 10) {
                return '+44 ' . substr($rest, 0, 4) . ' ' . substr($rest, 4, 3) . ' ' . substr($rest, 7, 3);
            }
        }
        
        // Generic formatting
        $formatted = '+' . $countryCode . ' ';
        $remaining = $rest;
        
        while (strlen($remaining) > 0) {
            $formatted .= substr($remaining, 0, 3) . ' ';
            $remaining = substr($remaining, 3);
        }
        
        return trim($formatted);
    }

    /**
     * Normalize to +254 format for storage (optional)
     */
    public static function normalizeForStorage($phoneNumber): string
    {
        if (empty($phoneNumber)) {
            return '';
        }

        $cleaned = preg_replace('/\s+/', '', $phoneNumber);
        
        // Fix +074... pattern
        if (preg_match('/^\+074(\d{7})$/', $cleaned, $matches)) {
            return '+2547' . $matches[1];
        }
        
        // Already in correct format
        if (preg_match('/^\+254\d{9}$/', $cleaned)) {
            return $cleaned;
        }
        
        // Add plus to 254...
        if (preg_match('/^254(\d{9})$/', $cleaned, $matches)) {
            return '+254' . $matches[1];
        }
        
        // Convert 07... to +2547...
        if (preg_match('/^07(\d{8})$/', $cleaned, $matches)) {
            return '+2547' . $matches[1];
        }
        
        // Convert 01... to +2541...
        if (preg_match('/^01(\d{8})$/', $cleaned, $matches)) {
            return '+2541' . $matches[1];
        }
        
        // Convert 7... to +2547...
        if (preg_match('/^7(\d{8})$/', $cleaned, $matches)) {
            return '+2547' . $matches[1];
        }
        
        // Convert 1... to +2541...
        if (preg_match('/^1(\d{8})$/', $cleaned, $matches)) {
            return '+2541' . $matches[1];
        }
        
        return $cleaned;
    }
}