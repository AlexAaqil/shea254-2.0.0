<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payments\KCBMpesaExpressController;
use App\Http\Controllers\Sales\SaleController;

Route::middleware('auth:sanctum')->get('user', function (Request $request) {
    return $request->user();
});

Route::post('payment/callback', [KCBMpesaExpressController::class, 'handleCallback'])->name('payments.kcb-callback');

Route::get('/paystack/callback', [SaleController::class, 'paystackCallback'])->name('paystack.callback');
Route::post('/paystack/webhook', [SaleController::class, 'paystackWebhook'])->name('paystack.webhook');
