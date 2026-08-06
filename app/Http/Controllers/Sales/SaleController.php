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
use App\Http\Controllers\Payments\PaystackController;
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
        $cart_subtotal = $cart->getSubtotal();

        $user = Auth::check() ? Auth::user() : null;

        $locations = DeliveryLocation::orderBy('location_name')->get();
        $areas = DeliveryArea::orderBy('area_name')->get();

        // Check if we have stored form data from a previous failed payment
        if (session()->has('checkout_form_data')) {
            // Merge stored data with old input for the view
            $stored_data = session('checkout_form_data');
            foreach ($stored_data as $key => $value) {
                if (!old($key)) {
                    request()->merge([$key => $value]);
                }
            }
        }

        // META TRACKING INITIATECHECKOUT EVENT
        // Get cart items for tracking
        $cart_items = $cart->getItems();
        $product_ids = [];

        foreach($cart_items as $item) {
            $product_ids[] = (string) $item->product->id;
        }

        // Dispatch the event to JavaScript
        session()->put('meta_initiate_checkout', [
            'value' => $cart_subtotal,
            'currency' => 'KES',
            'content_ids' => $product_ids,
            'num_items' => $cart_count
        ]);

        return view('pages.general.sales.checkout', compact('user', 'cart_count', 'cart_subtotal', 'locations', 'areas'));
    }

    public function store(CheckoutRequest $request, CartService $cart)
    {
        $validated_data = $request->validated();

        // Store form data in session before any payment processing to ensure we can repopulate the form if payment fails
        session()->put('checkout_form_data', $validated_data);

        // Store the selected delivery method details for later use
        if ($validated_data['delivery_method'] === 'delivery') {
            session()->put('checkout_selected_location', $validated_data['location']);
            session()->put('checkout_selected_area', $validated_data['area']);
        }

        $cart_items = $cart->getItems();
        $cart_subtotal = (float)$cart->getSubtotal();

        $phone_number = $validated_data['phone_number'];
        $email = $validated_data['email'];
        $full_name = $validated_data['full_name'];

        $delivery_method = $validated_data['delivery_method'];
        $shipping_cost = 0.0;

        $address = null;
        $additional_information = $validated_data['additional_information'] ?? null;
        $location_name = null;
        $area_name = null;

        $payment_method = $validated_data['payment_method'];

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

        $total_amount = $cart->getTotal($shipping_cost);
        $order_number = 'ord_' . Str::upper(Str::random(6)) . '_' . now()->format('dmy');
        $user_id = Auth::check() ? Auth::user()->id : null;

        if ($payment_method === 'kcb_mpesa') {
            return $this->processKcbMpesaPayment($validated_data, $cart_items, $total_amount, $order_number, $user_id, $shipping_cost, $address, $location_name, $area_name);
        } else {
            return $this->processPayStackPayment($validated_data, $cart_items, $total_amount, $order_number, $user_id, $shipping_cost, $address, $location_name, $area_name);
        }
    }

    private function processKcbMpesaPayment($validated_data, $cart_items, $total_amount, $order_number, $user_id, $shipping_cost, $address, $location_name, $area_name) 
    {
        try {
            $kcb_mpesa_express = app(KCBMpesaExpressController::class);
            $response = $kcb_mpesa_express->initiatePayment($validated_data['phone_number'], $total_amount, $order_number);
        } catch (Throwable $e) {
            report($e);
            session()->flash('notify', ['message' => 'Payment initialization failed. Please try again', 'type' => 'error']);
            return redirect()->route('checkout-page');
        }

        if (isset($response->response->ResponseCode) && $response->response->ResponseCode === '0') {
            $order = $this->createOrder(
                $validated_data,
                $cart_items,
                $total_amount,
                $order_number,
                $user_id,
                $shipping_cost,
                $address,
                $location_name,
                $area_name,
                'kcb_mpesa',
            );

            $this->createPaymentRecord($order, $response, 'kcb_mpesa', $order_number);

            Session::put(['order_number' => $order->order_number, 'order_id' => $order->id]);

            Session::forget(['cart', 'cart_count']);

            session()->flash('notify', ['message' => "Please complete the payment on your phone for {$order->order_number}.", 'type' => 'success']);
            return redirect()->route('checkout.success');
        }

        session()->flash('notify', ['message' => "{$response->response->CustomerMessage}. Payment initiation failed. Please try again.", 'type' => 'error']);
        return redirect()->route('checkout-page');
    }

    private function processPayStackPayment($validated_data, $cart_items, $total_amount, $order_number, $user_id, $shipping_cost, $address, $location_name, $area_name)
    {
        // Create order first with 'pending' status
        $order = $this->createOrder(
            $validated_data,
            $cart_items,
            $total_amount,
            $order_number,
            $user_id,
            $shipping_cost,
            $address,
            $location_name,
            $area_name,
            'paystack'
        );

        // Store order ID in session for reference on return
        session()->put('pending_paystack_order_id', $order->id);


        // Initialize Paystack transaction
        $paystackController = app(PaystackController::class);
        return $paystackController->initializeTransaction($order, $validated_data['email'], $total_amount);
    }

    private function createOrder($validated_data, $cart_items, $total_amount, $order_number, $user_id, $shipping_cost, $address, $location_name, $area_name, $payment_method)
    {
        $order = Sale::create([
            'order_number' => $order_number,
            'order_type' => 1,
            'discount_code' => null,
            'discount' => 0,
            'total_amount' => $total_amount,
            'payment_method' => $payment_method,
            'status' => 'payment_pending',
            'user_id' => $user_id,
        ]);

        OrderDelivery::create([
            'order_id' => $order->id,
            'full_name' => $validated_data['full_name'],
            'email' => $validated_data['email'],
            'phone_number' => $validated_data['phone_number'],
            'address' => $address,
            'additional_information' => $validated_data['additional_information'] ?? null,
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
                'selling_price' => $item->unit_price,
                'order_id'      => $order->id,
            ]);
        }

        return $order;
    }

    private function createPaymentRecord($order, $response, $gateway, $order_number)
    {
        $order->payment()->create([
            'payment_gateway' => $gateway,
            'merchant_request_id' => $response->response->MerchantRequestID ?? '',
            'checkout_request_id' => $response->response->CheckoutRequestID ?? '',
            'transaction_reference' => $order_number,
            'response_code' => $response->response->ResponseCode ?? '',
            'response_description' => $response->response->ResponseDescription ?? '',
            'customer_message' => $response->response->CustomerMessage ?? '',
            'status' => $response->response->ResponseCode === '0' ? 'pending' : 'failed',
            'order_id' => $order->id,
        ]);
    }

    public function success()
    {
        $order_number = session('order_number');
        $order = Sale::with(['payment', 'order_delivery', 'order_items'])
            ->where('order_number', $order_number)
            ->first();

        if (!$order) {
            session()->flash('notify', ['message' => "You currently have no successful order", 'type' => 'error']);
            return redirect()->route('shop-page');
        }

        session()->forget('order_number');

        if ($order->payment_method === 'kcb_mpesa') {
            return view('pages.general.sales.success-mpesa', compact('order_number', 'order'));
        } else {
            return view('pages.general.sales.success-paystack', compact('order_number', 'order'));
        }
    }

    public function requestSTKPush($order)
    {
        $order = Sale::where('order_number', $order)->firstOrFail();
        $payment = optional($order->payment);

        if ($payment->status === 'failed' || $payment->status === 'pending') {
            $amount = (int) round($order->total_amount);
            $phone_number = $order->order_delivery->phone_number;
            $order_number = $order->order_number;

            $kcb_mpesa_express = new KCBMpesaExpressController();
            $response = $kcb_mpesa_express->initiatePayment($phone_number, $amount, $order_number);

            if ($response->header->statusCode === '0') {
                $payment->update([
                    'merchant_request_id' => $response->response->MerchantRequestID,
                    'checkout_request_id' => $response->response->CheckoutRequestID,
                    'response_code' => $response->response->ResponseCode,
                    'response_description' => $response->response->ResponseDescription,
                    'customer_message' => $response->response->CustomerMessage,
                    'status' => $response->response->ResponseCode === '0' ? 'pending' : 'failed',
                ]);

                session()->flash('notify', ['message' => "Success. {$response->response->CustomerMessage}", 'type' => 'success']);
                return redirect()->back();
            }

            session()->flash('notify', ['message' => "Sorry. {$response->response->CustomerMessage}", 'type' => 'error']);
            return redirect()->back();
        }

        session()->flash('notify', ['message' => "Sorry. Cannot initiate payment at this time.", 'type' => 'error']);
        return redirect()->back();
    }
}
