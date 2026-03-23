# Payment Methods
SasaPay Network codes for payment:
- 63902 (MPesa)
- 63903 (AirtelMoney) 
- 63907 (T-Kash)

## Monitoring Dashboard
Sample code to be used for monitoring exhange rate apis health

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CurrencyExchangeService;
use Illuminate\Support\Facades\Log;

class MonitorExchangeRates extends Command
{
    protected $signature = 'monitor:exchange-rates';
    protected $description = 'Monitor exchange rate API health';

    public function handle(CurrencyExchangeService $exchangeService)
    {
        $result = $exchangeService->getRate();
        
        if ($result['source'] === 'fallback_config') {
            Log::critical('EXCHANGE RATE CRITICAL: All APIs failed, using fallback');
            
            // Send alert to admin
            // You could implement email/SMS notification here
        }
        
        $this->info("Current rate: {$result['rate']} from {$result['source']}");
    }
}
```