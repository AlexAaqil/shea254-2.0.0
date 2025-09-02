<x-app-layout>
    <section class="Hero">
        <div class="container">
            <h1>Billing Information</h1>
        </div>
    </section>

    <section class="CheckoutDetails">
        <div class="container">
            <div class="checkout_form">
                <div class="custom_form">
                    <form action="" method="post">
                        @csrf

                        <div class="inputs_group">
                            <div class="inputs">
                                <label for="full_name">Full Name</label>
                                <input type="text" name="full_name" id="full_name" placeholder="Enter your Full Name" value="{{ $user ? $user->full_name : old('full_name') }}">
                                <x-form-input-error field="full_name" />
                            </div>

                            <div class="inputs">
                                <label for="email">Email Address</label>
                                <input type="email" name="email" id="email" placeholder="example@gmail.com" value="{{ $user ? $user->email : old('email') }}">
                                <x-form-input-error field="email" />
                            </div>
                        </div>

                        <div class="inputs">
                            <label for="phone_number">Phone Number <span class="text-gray-100">(For payment)</span></label>
                            <input type="text" name="phone_number" id="phone_number" placeholder="2547xxxxxxxx" value="{{ $user ? $user->phone_number : old('phone_number') }}">
                            <x-form-input-error field="phone_number" />
                        </div>

                        <div class="inputs">
                            <label for="delivery_method">How would you like to receive your Order?</label>
                            <div class="custom_radio_buttons">
                                <label>
                                    <input class="option_radio" type="radio" name="delivery_method" value="delivery" checked>
                                    <span>Delivery</span>
                                </label>

                                <label>
                                    <input class="option_radio" type="radio" name="delivery_method" value="shop">
                                    <span>Pick it from the shop</span>
                                </label>
                            </div>
                            <x-form-input-error field="delivery_method" />
                        </div>

                        <div class="inputs_group delivery_details" id="delivery_details">
                            <div class="inputs">
                                <label for="location">Location</label>
                                <select name="location" id="location">
                                    <option value="">Select Location</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                                    @endforeach
                                </select>
                                <x-form-input-error field="location" />
                            </div>

                            <div class="inputs">
                                <label for="area">Area</label>
                                <select name="area" id="area">
                                    <option value="">Select Area</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                                    @endforeach
                                </select>
                                <x-form-input-error field="area" />
                            </div>
                        </div>

                        <div class="inputs">
                            <label for="address">Address</label>
                            <input type="text" name="address" id="address" placeholder="Enter delivery address" value="{{ old('address') }}">
                            <x-form-input-error field="address" />
                        </div>

                        <div class="inputs">
                            <label for="additional_information">Additional Information</label>
                            <input type="text" name="additional_information" id="additional_information" placeholder="Eg. Apartment name, directions, etc." value="{{ old('additional_information') }}">
                            <x-form-input-error field="additional_information" />
                        </div>

                        <button type="submit">Confirm Order</button>
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
                    <span id="shipping_cost">Ksh. {{ number_format($shipping_cost, 2) }}</span>
                </div>

                <div class="summary_item">
                    <span>Total Amount:</span>
                    <span id="total_amount">Ksh. {{ number_format($cart_total, 2) }}</span>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fbq('track', 'InitiateCheckout', {
                value: {{ $cart['subtotal'] }},
                currency: 'KES',
                num_items: {{ count($cart['items'] ?? []) }},
                content_ids: {!! json_encode(collect($cart['items'] ?? [])->pluck('product_id')->toArray()) !!},
                content_type: 'product',
            });

            const locationSelect = document.getElementById("location");
            const areaSelect = document.getElementById("area");
            const shippingCostElement = document.getElementById("shipping_cost_amount");
            const totalElement = document.getElementById("total_amount");
            const pick_up_method = document.querySelectorAll("input[name='pickup_method']");
            const delivery_details = document.getElementById("delivery_details");

            function togglePickupMethod() {
                if (pick_up_method[0].checked) {
                    delivery_details.style.display = 'block';
                } else {
                    delivery_details.style.display = 'none';
                }
            }

            togglePickupMethod();

            pick_up_method.forEach(function (radio) {
                radio.addEventListener('change', togglePickupMethod);
            });

            let areaPrice = 0;

            function updateShippingAndTotal() {
                const shippingCost = parseFloat(areaPrice);

                if (!isNaN(shippingCost)) {
                    const cartSubtotal = parseFloat("{{ $cart['subtotal'] }}");

                    const formattedShippingCost = shippingCost.toFixed(2);
                    const formattedTotal = (shippingCost + cartSubtotal).toFixed(2);

                    shippingCostElement.textContent = `Ksh. ${formattedShippingCost}`;
                    totalElement.textContent = `Ksh. ${formattedTotal}`;
                } else {
                    console.error("Invalid shipping cost:", areaPrice);
                }
            }

            locationSelect.addEventListener("change", function () {
                const selectedLocationId = this.value;

                fetch(`/areas/fetch/${selectedLocationId}`)
                    .then(response => response.json())
                    .then(data => {
                        areaSelect.innerHTML = "";
                        areaSelect.add(new Option("Select Area", ""));

                        data.forEach(area => {
                            areaSelect.add(new Option(area.area_name, area.id));
                        });
                    });
            });

            areaSelect.addEventListener("change", function () {
                const selectedAreaId = this.value;

                fetch(`/area/shipping-price/${selectedAreaId}`)
                    .then(response => response.json())
                    .then(data => {
                        areaPrice = data.price;

                        updateShippingAndTotal();
                    });
            });
        });
    </script>
@endpush
