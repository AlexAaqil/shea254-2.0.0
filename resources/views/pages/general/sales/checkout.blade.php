<x-guest-layout>
    <div class="CheckoutPage">
        <section class="Hero">
            <div class="container">
                <h1>Billing Information</h1>
            </div>
        </section>

        <section class="CheckoutDetails">
            <div class="container">
                <div class="checkout_form">
                    <div class="custom_form">
                        <form action="{{ Route::has('checkout.store') ? route('checkout.store') : '#' }}" method="post">
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
                                        <input class="option_radio" type="radio" name="delivery_method" value="delivery" {{ old('delivery_method', 'delivery') === 'delivery' ? 'checked' : '' }}>
                                        <span>Delivery</span>
                                    </label>

                                    <label>
                                        <input class="option_radio" type="radio" name="delivery_method" value="shop" {{ old('delivery_method') === 'shop' ? 'checked' : '' }}>
                                        <span>Pick it from the shop</span>
                                    </label>
                                </div>
                                <x-form-input-error field="delivery_method" />
                            </div>

                            <div class="delivery_details" id="delivery_details" style="display:none;">
                                <div class="inputs_group">
                                    <div class="inputs">
                                        <label for="location">Location</label>
                                        <select name="location" id="location">
                                            <option value="">Select Location</option>
                                            @foreach($locations as $location)
                                                <option value="{{ $location->id }}" {{ (string)old('location') === (string)$location->id ? 'selected' : '' }}>
                                                    {{ $location->location_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-form-input-error field="location" />
                                    </div>

                                    <div class="inputs">
                                        <label for="area">Area</label>
                                        <select name="area" id="area">
                                            <option value="">Select Area</option>
                                            @foreach($areas as $area)
                                                <option value="{{ $area->id }}" {{ (string)old('area') === (string)$area->id ? 'selected' : '' }}>
                                                    {{ $area->area_name }}
                                                </option>
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

        let areaPrice = 0;

        function updateShippingAndTotal() {
            const cartSubtotal = parseFloat("{{ (float)$cart_subtotal }}");
            const shippingCost = Number.isFinite(parseFloat(areaPrice)) ? parseFloat(areaPrice) : 0;
            shippingCostElement.textContent = `Ksh. ${shippingCost.toFixed(2)}`;
            totalElement.textContent = `Ksh. ${(cartSubtotal + shippingCost).toFixed(2)}`;
        }

        function togglePickupMethod() {
            const selected = document.querySelector("input[name='delivery_method']:checked").value;
            if (selected === 'delivery') {
                deliveryDetails.style.display = 'block';
            } else {
                deliveryDetails.style.display = 'none';
                areaPrice = 0; // reset shipping on pickup
            }
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
    });
    </script>
    @endpush
</x-guest-layout>
