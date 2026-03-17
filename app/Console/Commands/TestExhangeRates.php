<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CurrencyExchangeService;
use Exception;

class TestExhangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paypal:test-exchange-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all exchange rate APIs to see which ones are working';

    /**
     * Execute the console command.
     */
    public function handle(CurrencyExchangeService $exchangeService)
    {
        // Header
        $this->info('====================================');
        $this->info('Testing EXCHANGE RATE APIS');
        $this->info('====================================');

        $this->newLIne();
        $this->line('Fetching the best available rate...');
        $this->newLine();

        try {
            $result = $exchangeService->getRate();

            // Display results in a table
            $this->table(
                ['Source', 'Rate (KES to USD)', 'Timestamp', 'Status'],
                [[
                    $result['source'],
                    $result['rate'],
                    $result['timestamp']->format('d-m-Y H:i:s'),
                    isset($result['warning']) ? '⚠️ Fallback' : '✅ Success'
                ]]
            );

            $this->newLine();

            // Show any warnings
            if (isset($result['warning'])) {
                $this->warn('⚠️ Warning: ' . $result['warning']);
            }

            // Test conversion of common amounts
            $this->info('📊 Sample Conversions:');
            $this->newLine();

            $test_amounts = [1000, 2500, 5000, 10000];
            foreach ($test_amounts as $kes) {
                $usd = round($kes * $result['rate'], 2);
                $this->line("KES " . number_format($kes) . " -> USD $" . number_format($usd, 2));
            }

            $this->newLine();
            $this->info('✅ Test completed successfully!');
        } catch (Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }

        // Return success code
        return 0;
    }
}
