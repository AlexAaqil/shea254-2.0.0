<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class PayStackService
{
    protected $secret_key;
    protected $public_key;
    protected $base_url;

    public function __construct()
    {
        $this->secret_key = env('PAYSTACK_PUBLIC_KEY');
        $this->public_key = env('PAYSTACK_SECRET_KEY');
        $this->base_url = 'https://api.paystack.co';
    }

    /**
     * Initialize a transaction
     */
    public function initializeTransaction($email, $amount, $reference, $metadata = [])
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secret_key,
                'Content-Type' => 'application/json',
            ])->post($this->base_url . '/transaction/initialize', [
                'email' => $email,
                'amount' => $amount * 100, // Paystack uses kobo/cent
                'reference' => $reference,
                'metadata' => $metadata,
                'callback_url' => route('paystack.callback'),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * Verify a transaction
     */
    public function verifyTransaction($reference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secret_key,
                'Content-Type' => 'application/json',
            ])->get($this->base_url . '/transaction/verify/' . $reference);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * Get payment methods
     */
    public function getPaymentMethods()
    {
        return [
            'card' => 'Card Payment (Visa, Mastercard, Amex)',
        ];
    }
}