<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payments\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Sales\Sale;
use App\Models\Products\Product;

class KCBMpesaExpressController extends Controller
{
    protected $base_url;
    protected $authorization_url;
    protected $stkpush_url;
    protected $callback_url;
    protected $consumer_key;
    protected $consumer_secret;
    protected $shared_short_code;
    protected $org_short_code;
    protected $org_pass_key;
    protected $logger;
    protected $inventory_logger;

    public function __construct()
    {
        $this->base_url = env('KCB_BASE_URL');
        $this->authorization_url = env('KCB_AUTHORIZATION_URL');
        $this->stkpush_url = env('KCB_STKPUSH_URL');
        $this->callback_url = env('KCB_CALLBACK_URL');
        $this->consumer_key = env('KCB_CONSUMER_KEY');
        $this->consumer_secret = env('KCB_CONSUMER_SECRET');
        $this->shared_short_code = env('KCB_SHARED_SHORT_CODE');
        $this->org_short_code = env('KCB_ORG_SHORT_CODE');
        $this->org_pass_key = env('KCB_ORG_PASS_KEY');
        $this->logger = Log::channel('kcb_mpesa_express');
        $this->inventory_logger = Log::channel('inventory_management');

        $this->logger->info('KCBMpesaExpressController initialized', [
            'base_url' => $this->base_url,
            'callback_url' => $this->callback_url,
        ]);
    }

    public function getAuthorizationToken()
    {
        try {
            $credentials = base64_encode("{$this->consumer_key}:{$this->consumer_secret}");

            $response = Http::withHeaders([
                'Authorization' => "Basic {$credentials}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post("{$this->authorization_url}");

            $this->logger->info('Authorization Token Response: ', ['status' => $response->status(), 'body' => $response->body()]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'] ?? null;
            }

            $this->logger->error('Authorization Token Request Failed: ', ['status' => $response->status(), 'body' => $response->body()]);

            throw new Exception('Failed to get Authorization Token');
        } catch(Exception $e) {
            $this->logger->critical('Authorization Token Request Failed: ', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function initiatePayment($phone_number, $amount, $order_number)
    {
        try {
            $access_token = $this->getAuthorizationToken();
            if (!$access_token) {
                throw new Exception('Failed to get Authorization Token');
            }

            $this->logger->info('Using callback URL: ', ['url' => $this->callback_url]);

            $payload = [
                "phoneNumber" => $phone_number,
                "amount" => $amount,
                "invoiceNumber" => $this->shared_short_code . '-' . $order_number,
                "sharedShortCode" => true,
                "orgShortCode" => $this->org_short_code,
                "orgPassKey" => $this->org_pass_key,
                "callbackUrl" => $this->callback_url,
                "transactionDescription" => "Payment for order {$order_number}",
            ];

            $this->logger->info('STK Push Request Payload: ', [
                'url' => "{$this->stkpush_url}",
                'payload' => $payload,
                'headers' => [
                    'Authorization' => "Bearer {$access_token}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            $response = Http::withToken($access_token)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("{$this->stkpush_url}", $payload);

            $this->logger->info('Raw STK Push Response: ', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            $response_data = $response->json();

            $formatted_response = [
                'response' => [
                    'MerchantRequestID' => $response_data['response']['MerchantRequestID'] ?? null,
                    'ResponseCode' => $response_data['response']['ResponseCode'] ?? '1',
                    'CustomerMessage' => $response_data['response']['CustomerMessage'] ?? 'Processing failed',
                    'CheckoutRequestID' => $response_data['response']['CheckoutRequestID'] ?? false,
                    'ResponseDescription' => $response_data['response']['ResponseDescription'] ?? 'System error occurred'
                ],
                'header' => [
                    'statusDescription' => $response_data['header']['statusDescription'] ?? 'System error occurred',
                    'statusCode' => $response_data['header']['statusCode'] ?? '1'
                ]
            ];

            $this->logger->info('Formatted Response: ', ['response' => $formatted_response]);

            return json_decode(json_encode($formatted_response));
        } catch (Exception $e) {
            $this->logger->error('STK Push Error: ', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return (object) [
                'header' => (object) [
                    'statusCode' => '1',
                    'statusDescription' => $e->getMessage(),
                ],
                'response' => (object) [
                    'ResponseCode' => '1',
                    'ResponseDescription' => 'System error occured',
                    'CustomerMessage' => 'Sorry we could not process your payment. Please try again',
                ],
            ];
        }
    }

    public function handleCallback(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);

            $this->logger->info('Callback Received: ', ['data' => $data]);

            if (empty($data) || !isset($data['Body']['stkCallback'])) {
                throw new Exception('Invalid callback data structure');
            }

            $callback = $data['Body']['stkCallback'];
            $merchant_request_id = $callback['MerchantRequestID'] ?? null;
            $result_code = $callback['ResultCode'] ?? null;
            $result_desc = $callback['ResultDesc'] ?? null;

            if (!$merchant_request_id) {
                throw new Exception('Missing MerchantRequestID');
            }

            $payment = Payment::where('merchant_request_id', $merchant_request_id)->first();

            if (!$payment) {
                throw new Exception("Payment not found for MerchantRequestID: {$merchant_request_id}");
            }

            if ($result_code === 0) {
                $metadata = collect($callback['CallbackMetadata']['Item'] ?? [])
                    ->mapWithKeys(function ($item) {
                        return isset($item['Value']) ? [$item['Name'] => $item['Value']] : [$item['Name'] => null];
                    })->toArray();

                $payment->transaction_reference = $metadata['MpesaReceiptNumber'] ?? $payment->transaction_reference;
                $payment->response_description = json_encode([
                    'amount' => $metadata['Amount'] ?? null,
                    'mpesa_receipt' => $metadata['MpesaReceiptNumber'] ?? null,
                    'transaction_date' => $metadata['TransactionDate'] ?? null,
                    'phone_number' => $metadata['PhoneNumber'] ?? null
                ]);
                $payment->status = 'paid';
                $payment->response_code = $result_code;
                $payment->customer_message = 'Payment completed successfully';

                DB::transaction(function () use ($payment) {
                    $order = Sale::with('order_items')->find($payment->order_id);

                    if ($order && $order->order_items->isNotEmpty()) {
                        foreach($order->order_items as $item) {
                            $updated = Product::where('id', $item->product_id)
                                ->where('stock_count', '>=', $item->quantity)
                                ->decrement('stock_count', $item->quantity);

                            if ($updated) {
                                $this->inventory_logger->info('Stock decremented', [
                                    'product_id' => $item->product_id,
                                    'qty' => $item->quantity,
                                    'order_id' => $order->id,
                                ]);
                            } else {
                                $this->inventory_logger->warning('Stock deduction failed, not enough stock', [
                                    'product_id' => $item->product_id,
                                    'qty' => $item->quantity,
                                    'order_id' => $order->id,
                                ]);
                                // Optional: mark order as "on_hold" or notify admin
                            }
                        }
                    }
                });
            } else {
                $payment->status = 'failed';
                $payment->response_code = $result_code;
                $payment->response_description = $result_desc;
                $payment->customer_message = 'Payment failed: ' . $result_desc;
            }

            $payment->save();

            $this->logger->info('Payment Updated', [
                'merchant_request_id' => $merchant_request_id,
                'status' => $payment->status,
                'response_description' => $payment->response_description
            ]);

            return response()->json(['status' => 'success'], 200);
        } catch (Exception $e) {
            $this->logger->error('Callback Processing Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
