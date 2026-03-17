<x-guest-layout>
    <div class="CheckoutPage">
        <section class="Hero">
            <div class="container">
                <h1>Checkout</h1>
            </div>
        </section>

        <section class="CheckoutDetails">
            <div class="container">
                <div class="checkout_form">
                    <div class="custom_form">
                        <form action="{{ Route::has('checkout.store') ? route('checkout.store') : '#' }}" method="post">
                            @csrf

                            <div class="form_section contact_information_section">
                                <div class="form_section_header">
                                    <p>
                                        <span>1</span>
                                        <span>Contact Information</span>
                                    </p>
                                </div>
                                <div class="inputs_group">
                                    <div class="inputs">
                                        <label for="full_name" class="required">Full Name</label>
                                        <input type="text" name="full_name" id="full_name" placeholder="Enter your Full Name" value="{{ $user ? $user->full_name : old('full_name') }}">
                                        <x-form-input-error field="full_name" />
                                    </div>

                                    <div class="inputs">
                                        <label for="email" class="required">Email Address</label>
                                        <input type="email" name="email" id="email" placeholder="example@gmail.com" value="{{ $user ? $user->email : old('email') }}">
                                        <x-form-input-error field="email" />
                                    </div>
                                </div>

                                <div class="inputs_group">
                                    <div class="inputs">
                                        <label for="phone_number" class="required">Phone Number <span class="text-gray-400">(For payment)</span></label>
                                        <input type="text" name="phone_number" id="phone_number" placeholder="2547xxxxxxxx" value="{{ $user ? $user->phone_number : old('phone_number') }}">
                                        <x-form-input-error field="phone_number" />
                                    </div>
                                </div>
                            </div>

                            <div class="form_section delivery_method_section">
                                <div class="form_section_header">
                                    <p>
                                        <span>2</span>
                                        <span>Delivery Method</span>
                                    </p>
                                </div>
                                <div class="inputs">
                                    {{-- <label for="delivery_method" class="required">How would you like to receive your Order?</label> --}}
                                    <div class="custom_radio_buttons">
                                        <label>
                                            <input class="option_radio" type="radio" name="delivery_method" value="shop" {{ old('delivery_method', 'shop') === 'shop' ? 'checked' : '' }}>
                                            <span>Shop</span>
                                        </label>

                                        <label>
                                            <input class="option_radio" type="radio" name="delivery_method" value="delivery" {{ old('delivery_method') === 'delivery' ? 'checked' : '' }}>
                                            <span>Delivery</span>
                                        </label>
                                    </div>
                                    <x-form-input-error field="delivery_method" />
                                </div>

                                <div class="delivery_details" id="delivery_details" style="display:none;">
                                    <div class="inputs_group">
                                        <div class="inputs">
                                            <label for="location" class="required">Location</label>
                                            <select name="location" id="location">
                                                <option value="">Select Location</option>
                                                @foreach($locations as $location)
                                                    <option value="{{ $location->id }}" {{ (string)old('location', session('checkout_selected_location')) === (string)$location->id ? 'selected' : '' }}>
                                                        {{ $location->location_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-form-input-error field="location" />
                                        </div>

                                        <div class="inputs">
                                            <label for="area" class="required">Area</label>
                                            <select name="area" id="area">
                                                <option value="">Select Area</option>
                                                @foreach($areas as $area)
                                                    <option value="{{ $area->id }}" {{ (string)old('area', session('checkout_selected_area')) === (string)$area->id ? 'selected' : '' }}>
                                                        {{ $area->area_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-form-input-error field="area" />
                                        </div>
                                    </div>

                                    <div class="inputs">
                                        <label for="address" class="required">Address</label>
                                        <input type="text" name="address" id="address" placeholder="Enter delivery address (apartment name, directions)" value="{{ old('address') }}">
                                        <x-form-input-error field="address" />
                                    </div>
                                </div>
                            </div>

                            <div class="form_section payment_method_section">
                                <div class="form_section_header">
                                    <p>
                                        <span>3</span>
                                        <span>Payment Method</span>
                                    </p>
                                </div>

                                <div class="inputs">
                                    <div class="custom_radio_buttons">
                                        <label>
                                            <input class="option_radio" type="radio" name="payment_method" value="kcb_mpesa" {{ old('payment_method', 'kcb_mpesa') === 'kcb_mpesa' ? 'checked' : '' }}>
                                            <div class="image">
                                                <img src="{{ asset("assets/images/brand-icons/icon-mpesa-2.svg") }}" alt="Mpesa Icon" />
                                            </div>
                                            {{-- <span>MPesa</span> --}}
                                        </label>

                                        <label>
                                            <input class="option_radio" type="radio" name="payment_method" value="paystack" {{ old('payment_method') === 'paystack' ? 'checked' : '' }}>
                                            <div class="images">
                                                <div class="image">
                                                    <img src="{{ asset("assets/images/brand-icons/icon-visa.svg") }}" alt="Visa Icon" />
                                                </div>

                                                <div class="image">
                                                    <img src="{{ asset("assets/images/brand-icons/icon-mastercard.svg") }}" alt="Mastercard Icon" />
                                                </div>

                                                <div class="image">
                                                    <img src="{{ asset("assets/images/brand-icons/icon-amex.svg") }}" alt="Amex Icon" />
                                                </div>
                                            </div>
                                            {{-- <span>Card</span> --}}
                                        </label>

                                        <label>
                                            <input class="option_radio" type="radio" name="payment_method" value="paypal" {{ old('payment_method') === 'paypal' ? 'checked' : '' }}>
                                            {{-- <div class="image">
                                                <img src="{{ asset("assets/images/brand-icons/icon-paypal.svg") }}" alt="PayPal Icon">
                                            </div> --}}
                                            <span class="paypal_button">
                                                <span>Pay</span>
                                                <span>Pal</span>
                                            </span>
                                        </label>

                                        {{-- <label>
                                            <input class="option_radio" type="radio" name="payment_method" value="paypal" {{ old('payment_method') === 'paypal' ? 'checked' : '' }}>
                                            <span>Paypal</span>
                                        </label> --}}
                                    </div>
                                    <x-form-input-error field="payment_method" />
                                </div>

                                <div id="mpesa_info" class="payment_info_box mpesa_payment_info_box">
                                    <p style="margin: 0;">An STK Push will be sent to <span id="phone_display">{{ old('phone_number', $user?->phone_number) ?: 'your phone' }}</span>. Enter your PIN to complete payment.</p>
                                </div>

                                <div id="paystack_info" class="payment_info_box paystack_payment_info_box">
                                    <p style="margin: 0 0 10px;">You'll be redirected to Paystack's secure payment page where you can enter your card details.</p>
                                    <p style="margin: 0; font-size: 14px;">We accept:</p>
                                    <ul style="margin: 5px 0 0 20px;">
                                        <li>
                                            <div class="image">
                                                <img src="{{ asset('assets/images/brand-icons/icon-visa.svg') }}" alt="Visa Icon" />
                                            </div>
                                            <span>Visa</span>
                                        </li>
                                        <li>
                                            <div class="image">
                                                <img src="{{ asset('assets/images/brand-icons/icon-mastercard.svg') }}" alt="Mastercard Icon" />
                                            </div>
                                            <span>Mastercard</span>
                                        </li>
                                        <li>
                                            <div class="image">
                                                <img src="{{ asset("assets/images/brand-icons/icon-amex.svg") }}" alt="Amex Icon">
                                            </div>
                                            <span>American Express</span>
                                        </li>
                                    </ul>
                                </div>

                                <div id="paypal_info" class="payment_info_box paypal_payment_info_box" style="display: none;">
                                    <p style="margin: 0 0 10px;">You'll be redirected to PayPal's secure payment page where you can:</p>
                                    <ul style="margin: 5px 0 0 20px;">
                                        <li>Pay with your PayPal balance</li>
                                        <li>Pay with credit/debit card (Visa, MasterCard, Amex, Discover)</li>
                                        <li>Pay with bank account</li>
                                    </ul>
                                    <p style="margin: 10px 0 0; font-size: 14px;">No PayPal account? You can still pay with your card.</p>
                                </div>
                            </div>

                            <div class="inputs">
                                <label for="additional_information">Additional Information</label>
                                <input type="text" name="additional_information" id="additional_information" placeholder="Any extra information about your order" value="{{ old('additional_information') }}">
                                <x-form-input-error field="additional_information" />
                            </div>

                            <div class="buttons mt-4">
                                <button type="submit">Confirm Order</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="checkout_summary">
                    <h2>Order Summary</h2>

                    <div class="summary_item">
                        <span>Total Items:</span>
                        <span>{{ $cart_count }}</span>
                    </div>

                    <div class="summary_item">
                        <span>Shipping Cost:</span>
                        <span id="shipping_cost">Ksh. 0.00</span>
                    </div>

                    <div class="summary_item">
                        <span>Total Amount:</span>
                        <span id="total_amount">Ksh. {{ number_format($cart_subtotal, 2) }}</span>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const locationSelect = document.getElementById("location");
        const areaSelect = document.getElementById("area");
        const shippingCostElement = document.getElementById("shipping_cost");
        const totalElement = document.getElementById("total_amount");
        const deliveryDetails = document.getElementById("delivery_details");
        const paystackInfo = document.getElementById("paystack_info");
        const mpesaInfo = document.getElementById("mpesa_info");
        const paypalInfo = document.getElementById("paypal_info");

        let areaPrice = 0;

        function updateShippingAndTotal() {
            const cartSubtotal = parseFloat("{{ (float)$cart_subtotal }}");
            const shippingCost = Number.isFinite(parseFloat(areaPrice)) ? parseFloat(areaPrice) : 0;
            shippingCostElement.textContent = `Ksh. ${shippingCost.toFixed(2)}`;
            totalElement.textContent = `Ksh. ${(cartSubtotal + shippingCost).toFixed(2)}`;
        }

        function togglePickupMethod() {
            // Ensure delivery details visibility matches selected radio on page load
            const selectedDelivery = document.querySelector("input[name='delivery_method']:checked")?.value;
            const deliveryDetails = document.getElementById("delivery_details");

            if (selectedDelivery === 'delivery') {
                deliveryDetails.style.display = 'block';
            } else {
                deliveryDetails.style.display = 'none';
                areaPrice = 0; // reset shipping on pickup
            }

            // If we have old location/area values, trigger the area load
            @if(old('location') || session('checkout_selected_location'))
                setTimeout(function() {
                    const locationSelect = document.getElementById("location");
                    if (locationSelect) {
                        locationSelect.dispatchEvent(new Event('change'));
                        
                        @if(old('area') || session('checkout_selected_area'))
                            setTimeout(function() {
                                const areaSelect = document.getElementById("area");
                                if (areaSelect) {
                                    areaSelect.value = "{{ old('area', session('checkout_selected_area')) }}";
                                    areaSelect.dispatchEvent(new Event('change'));
                                }
                            }, 500);
                        @endif
                    }
                }, 100);
            @endif
            updateShippingAndTotal();
        }

        // Initial toggle state (respect old input from validation failure)
        togglePickupMethod();

        document.querySelectorAll("input[name='delivery_method']").forEach(radio => {
            radio.addEventListener('change', togglePickupMethod);
        });

        // When location changes, fetch areas for that location
        locationSelect.addEventListener("change", function () {
            const selectedLocationId = this.value;
            areaSelect.innerHTML = "";
            areaSelect.add(new Option("Select Area", ""));

            if (!selectedLocationId) {
                areaPrice = 0;
                updateShippingAndTotal();
                return;
            }

            fetch(`/areas/fetch/${selectedLocationId}`)
                .then(r => r.json())
                .then(data => {
                    data.forEach(area => {
                        const opt = new Option(area.area_name, area.id);
                        areaSelect.add(opt);
                    });
                })
                .catch(() => {
                    // Fail safe
                    areaSelect.innerHTML = "";
                    areaSelect.add(new Option("Select Area", ""));
                });
        });

        // When area changes, fetch shipping price
        areaSelect.addEventListener("change", function () {
            const selectedAreaId = this.value;
            if (!selectedAreaId) {
                areaPrice = 0;
                updateShippingAndTotal();
                return;
            }
            fetch(`/areas/shipping-cost/${selectedAreaId}`)
                .then(r => r.json())
                .then(data => {
                    areaPrice = data.price || 0;
                    updateShippingAndTotal();
                })
                .catch(() => {
                    areaPrice = 0;
                    updateShippingAndTotal();
                });
        });

        // If old('location') and old('area') exist, trigger the initial totals
        @if(old('location'))
            locationSelect.dispatchEvent(new Event('change'));
        @endif
        @if(old('area'))
            areaSelect.value = "{{ old('area') }}";
            areaSelect.dispatchEvent(new Event('change'));
        @endif

        function togglePaymentInfo() {
            const selectedPayment = document.querySelector("input[name='payment_method']:checked")?.value;

            mpesaInfo.style.display = 'none';
            paystackInfo.style.display = 'none';
            paypalInfo.style.display = 'none';
            
            if (selectedPayment === 'paypal') {
                paypalInfo.style.display = 'block';
            } else if (selectedPayment === 'paystack') {
                paystackInfo.style.display = 'block';
            } else {
                mpesaInfo.style.display = 'block';
            }

            // Update the phone number in M-Pesa info
            if (mpesaInfo.style.display !== 'none') {
                const phoneDisplay = document.getElementById('phone_display');
                const phoneInput = document.querySelector('input[name="phone_number"]');
                if (phoneDisplay && phoneInput) {
                    phoneDisplay.textContent = phoneInput.value || 'your phone';
                }
            }
        }

        const paymentMethodRadios = document.querySelectorAll("input[name='payment_method']");
    
        paymentMethodRadios.forEach(radio => {
            radio.addEventListener('change', togglePaymentInfo);
        });

        togglePaymentInfo();

        // Update phone number in M-Pesa info when phone number changes
        const phoneInput = document.querySelector('input[name="phone_number"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                const selectedPayment = document.querySelector("input[name='payment_method']:checked")?.value;
                if (selectedPayment !== 'paystack') {
                    const phoneDisplay = document.getElementById('phone_display');
                    if (phoneDisplay) {
                        phoneDisplay.textContent = this.value || 'your phone';
                    }
                }
            });
        }
    });
    </script>
    @endpush
</x-guest-layout>
