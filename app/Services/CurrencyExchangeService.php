<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class CurrencyExchangeService
{
    protected $logger;
    protected $fallback_rate;
    protected $apis;
    
    public function __construct()
    {
        $this->logger = Log::channel('paypal');
        $this->fallback_rate = (float) env('FALLBACK_KES_TO_USD_RATE', 0.0077);
        
        // Multiple API sources for redundancy
        $this->apis = [
            'primary' => [
                'url' => 'https://api.exchangerate-api.com/v4/latest/KES',
                'params' => [],
                'response_path' => 'rates.USD',
                'headers' => []
            ],
            'secondary' => [
                'url' => 'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/kes.json',
                'params' => [],
                'response_path' => 'kes.usd',
                'headers' => []
            ],
            'tertiary' => [
                'url' => 'https://open.er-api.com/v6/latest/KES',
                'params' => [],
                'response_path' => 'rates.USD'
            ],
            'quaternary' => [
                'url' => 'https://latest.currency-api.pages.dev/v1/currencies/kes.min.json',
                'params' => [],
                'response_path' => 'kes.usd',
                'headers' => []
            ]
        ];
    }

    /**
     * Get the best available exchange rate with fallbacks
     */
    public function getRate(): array
    {
        // Try cache first (5-minute cache for rate stability)
        $cached = $this->getCachedRate();
        if ($cached) {
            return $cached;
        }

        // Try each API in sequence
        foreach ($this->apis as $source => $config) {
            try {
                $rate = $this->fetchFromApi($config);
                
                if ($rate && $rate > 0) {
                    $result = [
                        'rate' => $rate,
                        'source' => $source,
                        'timestamp' => now(),
                        'expires_at' => now()->addMinutes(5)
                    ];
                    
                    // Cache this successful rate
                    $this->cacheRate($result);
                    
                    $this->logger->info('Exchange rate fetched', $result);
                    
                    return $result;
                }
            } catch (Exception $e) {
                $this->logger->warning("Exchange rate API failed: {$source}", [
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        // If all APIs fail, use fallback but log critically
        $this->logger->critical('All exchange rate APIs failed, using fallback rate');
        
        return [
            'rate' => $this->fallback_rate,
            'source' => 'fallback_config',
            'timestamp' => now(),
            'expires_at' => now()->addMinutes(5),
            'warning' => 'Using configured fallback rate - APIs unavailable'
        ];
    }

    /**
     * Lock rate for a specific transaction
     */
    public function lockRateForTransaction(string $transactionId, array $rateData): void
    {
        Cache::put(
            "locked_rate_{$transactionId}", 
            $rateData, 
            now()->addHours(24) // Lock for 24 hours
        );
    }

    /**
     * Get locked rate for transaction
     */
    public function getLockedRate(string $transactionId): ?array
    {
        return Cache::get("locked_rate_{$transactionId}");
    }

    /**
     * Convert amount with rate locking
     */
    public function convertForTransaction(string $transactionId, float $amountKES): array
    {
        // Check if we already have a locked rate for this transaction
        $rateData = $this->getLockedRate($transactionId);
        
        if (!$rateData) {
            // Get fresh rate and lock it
            $rateData = $this->getRate();
            $this->lockRateForTransaction($transactionId, $rateData);
        }

        $amountUSD = round($amountKES * $rateData['rate'], 2);

        return [
            'kes_amount' => $amountKES,
            'usd_amount' => $amountUSD,
            'rate_used' => $rateData['rate'],
            'rate_source' => $rateData['source'],
            'rate_timestamp' => $rateData['timestamp'],
            'transaction_id' => $transactionId
        ];
    }

    /**
     * Fetch rate from specific API
     */
    protected function fetchFromApi(array $config): ?float
    {
        $response = Http::timeout(5)
            ->retry(2, 100)
            ->get($config['url'], $config['params']);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();
        
        // Navigate response path
        $value = $data;
        foreach (explode('.', $config['response_path']) as $key) {
            if (!isset($value[$key])) {
                return null;
            }
            $value = $value[$key];
        }

        return (float) $value;
    }

    /**
     * Get cached rate if not expired
     */
    protected function getCachedRate(): ?array
    {
        return Cache::get('current_exchange_rate');
    }

    /**
     * Cache the rate
     */
    protected function cacheRate(array $rateData): void
    {
        Cache::put('current_exchange_rate', $rateData, now()->addMinutes(5));
    }
}