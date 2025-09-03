<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\Sale;
use App\Models\Sales\OrderDelivery;
use App\Models\Sales\OrderItem;
use App\Models\Deliveries\DeliveryLocation;
use App\Models\Deliveries\DeliveryArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CartService;
use App\Http\Requests\Sales\CheckoutRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Payments\KCBMpesaExpressController;
use Throwable;

class SaleController extends Controller
{
    public function checkout(CartService $cart)
    {
        if ($cart->count() === 0) {
            session()->flash('notify', ['message' => 'Your cart is empty. Please add items to your cart before proceeding to checkout.']);
            return redirect()->route('shop-page');
        }

        $cart_count = $cart->count();
        $cart_total = $cart->getTotal();

        $user = Auth::check() ? Auth::user() : null;

        $locations = DeliveryLocation::orderby('location_name')->get();
        $areas = DeliveryArea::orderby('area_name')->get();

        return view('pages.general.sales.checkout', compact('user', 'cart_count', 'cart_total', 'locations', 'areas'));
    }

    public function store(CheckoutRequest $request, CartService $cart)
    {
        $validated_data = $request->validated();

        $cart_items = $cart->getItems();
        $cart_total = (float)$cart->getTotal();

        $phone_number = $validated_data['phone_number'];
        $email = $validated_data['email'];
        $full_name = $validated_data['full_name'];

        $delivery_method = $validated_data['delivery_method'];
        $shipping_cost = 0.0;

        $address = null;
        $additional_information = $validated_data['additional_information'] ?? null;
        $location_name = null;
        $area_name = null;

        if ($delivery_method === 'delivery') {
            $location = DeliveryLocation::findOrFail($validated_data['location']);
            $area = DeliveryArea::findOrFail($validated_data['area']);

            $address = $validated_data['address'];
            $location_name = $location->location_name;
            $area_name = $area->area_name;
            $shipping_cost = (float)$area->price;
        } else {
            $address = 'Shop';
            $location_name = 'Shop';
            $area_name = 'Shop';
            $shipping_cost = 0.0;
        }

        $total_amount = $shipping_cost + $cart_total;
        $order_number = 'ord_' . Str::upper(Str::random(6)) . '_' . now()->format('dmy');
        $user_id = Auth::check() ? Auth::user()->id : null;

        try {
            $kcb_mpesa_express = app(KCBMpesaExpressController::class);
            $response = $kcb_mpesa_express->initiatePayment($phone_number, $total_amount, $order_number);
        } catch (Throwable $e) {
            report($e);
            session()->flash('notify', ['message' => 'Payment initiation failed. Please try again', 'type' => 'error']);
            return redirect()->route('checkout-page');
        }

        if (isset($response->response->ResponseCode) && $response->response->ResponseCode === '0') {
            $order = Sale::create([
                'order_number' => $order_number,
                'order_type' => 1,
                'discount_code' => null,
                'discount' => 0,
                'total_amount' => $total_amount,
                'payment_method' => 'kcb_mpesa',
                'user_id' => $user_id,
            ]);

            OrderDelivery::create([
                'order_id' => $order->id,
                'full_name' => $full_name,
                'email' => $email,
                'phone_number' => $phone_number,
                'address' => $address,
                'additional_information' => $additional_information,
                'location' => $location_name,
                'area' => $area_name,
                'shipping_cost' => $shipping_cost,
            ]);

            foreach ($cart_items as $item) {
                OrderItem::create([
                    'product_id'    => $item->product->id,
                    'title'         => $item->product->title,
                    'quantity'      => $item->quantity,
                    'buying_price'  => $item->product->buying_price,
                    'selling_price' => $item->product->selling_price,
                    'order_id'      => $order->id,
                ]);
            }

            $order->payment()->create([
                'payment_gateway' => 'kcb_mpesa',
                'merchant_request_id' => $response->response->MerchantRequestID ?? '',
                'checkout_request_id' => $response->response->CheckoutRequestID ?? '',
                'transaction_reference' => $order_number,
                'response_code' => $response->response->ResponseCode ?? '',
                'response_description' => $response->response->ResponseDescription ?? '',
                'customer_message' => $response->response->CustomerMessage ?? '',
                'status' => $response->response->ResponseCode === '0' ? 'pending' : 'failed',
                'order_id' => $order->id,
            ]);

            Session::put([
                'order_number' => $order->order_number,
                'order_id' => $order->id
            ]);

            Session::forget(['cart', 'cart_count']);

            session()->flash('notify', ['message' => "Please complete the payment on your phone for {$order->order_number}.", 'type' => 'success']);
            return redirect()->route('checkout.success');
        }

        session()->flash('notify', ['message' => "{$response->response->CustomerMessage} ?? 'Payment initiation failed. Please try again.'", 'type' => 'error']);
        return redirect()->route('checkout.create');
    }

    public function success()
    {
        $order_number = session('order_number');
        $order = Sale::with(['payment', 'order_delivery', 'order_items'])
            ->where('order_number', $order_number)
            ->first();

        if (!$order) {
            session()->flash('notify', ['message' => 'Order not found', 'type' => 'error']);
            return redirect()->route('shop');
        }

        return view('pages.general.sales.success', compact('order_number', 'order'));
    }
}
