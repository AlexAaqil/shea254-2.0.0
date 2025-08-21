<x-app-layout>
    @include('partials.navbar')
    <main class="Checkout Cart" style="background: linear-gradient(135deg, #f8fafc, #ffffff); padding: 4em 1em;">
        <div class="container" style="max-width: 1200px; margin: 0 auto;">
            @include('partials.messages')
            <div class="header" style="text-align: center; margin-bottom: 3em;">
                <h1 style="font-size: clamp(1.8rem, 5vw, 2.5rem); color: #1a237e; font-weight: 800;">Billing information</h1>
            </div>

            <div class="body" style="display: flex; flex-direction: column; gap: 3em;">
                <div class="custom_form" style="background: white; border-radius: 24px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02); padding: 2em;">
                    <form action="" method="post">
                        @csrf

                        <div class="row_input_group" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5em;">
                            <div class="input_group">
                                <label for="full_name" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #1a237e; font-weight: 600;">Full Name</label>
                                <input type="text" name="full_name" id="full_name" placeholder="Enter your Full Name" value="{{ $user ? $user->first_name . ' ' . $user->last_name : old('full_name') }}" style="padding: 0.75em; font-size: clamp(0.9rem, 4vw, 1rem); border: 1px solid #e2e8f0; border-radius: 12px;">
                                <span class="inline_alert" style="font-size: clamp(0.8rem, 3.5vw, 0.9rem); color: #ef4444;">{{ $errors->first('full_name') }}</span>
                            </div>

                            <div class="input_group">
                                <label for="email" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #1a237e; font-weight: 600;">Email Address</label>
                                <input type="email" name="email" id="email" placeholder="example@gmail.com" value="{{ $user ? $user->email : old('email') }}" style="padding: 0.75em; font-size: clamp(0.9rem, 4vw, 1rem); border: 1px solid #e2e8f0; border-radius: 12px;">
                                <span class="inline_alert" style="font-size: clamp(0.8rem, 3.5vw, 0.9rem); color: #ef4444;">{{ $errors->first('email') }}</span>
                            </div>
                        </div>

                        <div class="row_input_group" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5em;">
                            <div class="input_group">
                                <label for="phone_number" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #1a237e; font-weight: 600;">Phone Number <span class="details" style="font-size: clamp(0.8rem, 3.5vw, 0.9rem); color: #64748b;">(To be used for payment)</span></label>
                                <input type="number" name="phone_number" id="phone_number" placeholder="2547xxxxxxxx" value="{{ $user ? $user->phone_number : old('phone_number') }}" style="padding: 0.75em; font-size: clamp(0.9rem, 4vw, 1rem); border: 1px solid #e2e8f0; border-radius: 12px;">
                                <span class="inline_alert" style="font-size: clamp(0.8rem, 3.5vw, 0.9rem); color: #ef4444;">{{ $errors->first('phone_number') }}</span>
                            </div>
                        </div>

                        <!-- <div class="input_group">
                            <label for="status" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #1a237e; font-weight: 600;">Choose your preferred payment method</label>
                            <div class="custom_radio_buttons" style="display: flex; flex-wrap: wrap; gap: 1em; margin-top: 0.5em;">
                                <label style="display: flex; align-items: center; gap: 0.5em; font-size: clamp(0.9rem, 4vw, 1rem);">
                                    <input class="option_radio" type="radio" name="payment_method" id="delivery" value="63902" checked>
                                    <span>Mpesa</span>
                                </label>

                                <label style="display: flex; align-items: center; gap: 0.5em; font-size: clamp(0.9rem, 4vw, 1rem);">
                                    <input class="option_radio" type="radio" name="payment_method" id="airtel_money" value="63903">
                                    <span>Airtel Money</span>
                                </label>

                                <label style="display: flex; align-items: center; gap: 0.5em; font-size: clamp(0.9rem, 4vw, 1rem);">
                                    <input class="option_radio" type="radio" name="payment_method" id="t_kash" value="63907">
                                    <span>T-Kash</span>
                                </label>
                            </div>
                        </div> -->

                        <div class="input_group">
                            <label for="status" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #1a237e; font-weight: 600;">How Would you like to receive your Order?</label>
                            <div class="custom_radio_buttons" style="display: flex; flex-wrap: wrap; gap: 1em; margin-top: 0.5em;">
                                <label style="display: flex; align-items: center; gap: 0.5em; font-size: clamp(0.9rem, 4vw, 1rem);">
                                    <input class="option_radio" type="radio" name="pickup_method" id="delivery" value="delivery" checked>
                                    <span>Delivery</span>
                                </label>

                                <label style="display: flex; align-items: center; gap: 0.5em; font-size: clamp(0.9rem, 4vw, 1rem);">
                                    <input class="option_radio" type="radio" name="pickup_method" id="shop" value="shop">
                                    <span>Pick it from the shop</span>
                                </label>
                            </div>
                        </div>

                        <div class="delivery_details" id="delivery_details" style="margin-top: 2em;">
                            <div class="row_input_group" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5em;">
                                <div class="input_group">
                                    <label for="location" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #1a237e; font-weight: 600;">Location</label>
                                    <select name="location" id="location" style="padding: 0.75em; font-size: clamp(0.9rem, 4vw, 1rem); border: 1px solid #e2e8f0; border-radius: 12px;">
                                        <option value="">Select Location</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="inline_alert" style="font-size: clamp(0.8rem, 3.5vw, 0.9rem); color: #ef4444;">{{ $errors->first('location') }}</span>
                                </div>

                                <div class="input_group">
                                    <label for="area" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #1a237e; font-weight: 600;">Area</label>
                                    <select name="area" id="area" style="padding: 0.75em; font-size: clamp(0.9rem, 4vw, 1rem); border: 1px solid #e2e8f0; border-radius: 12px;">
                                        <option value="">Select Area</option>
                                        @foreach($areas as $area)
                                            <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="inline_alert" style="font-size: clamp(0.8rem, 3.5vw, 0.9rem); color: #ef4444;">{{ $errors->first('area') }}</span>
                                </div>
                            </div>

                            <div class="input_group" style="margin-top: 1.5em;">
                                <label for="address" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #1a237e; font-weight: 600;">Address</label>
                                <input type="text" name="address" id="address" value="{{ $user ? $user->address : old('address') }}" placeholder="Enter the address your order should be delivered to" style="padding: 0.75em; font-size: clamp(0.9rem, 4vw, 1rem); border: 1px solid #e2e8f0; border-radius: 12px;">
                                <span class="inline_alert" style="font-size: clamp(0.8rem, 3.5vw, 0.9rem); color: #ef4444;">{{ $errors->first('address') }}</span>
                            </div>

                            <div class="input_group" style="margin-top: 1.5em;">
                                <label for="additional_information" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #1a237e; font-weight: 600;">Additional Information</label>
                                <input type="text" name="additional_information" id="additional_information" placeholder="Extra Information about the order... (e.g) Specific Location" value="{{ $user ? $user->additional_information : old('additional_information') }}" style="padding: 0.75em; font-size: clamp(0.9rem, 4vw, 1rem); border: 1px solid #e2e8f0; border-radius: 12px;">
                                <span class="inline_alert" style="font-size: clamp(0.8rem, 3.5vw, 0.9rem); color: #ef4444;">{{ $errors->first('additional_information') }}</span>
                            </div>
                        </div>

                        <button type="submit" style="display: inline-block; background: #1a237e; color: white; text-decoration: none; padding: 0.75em 1.5em; border-radius: 24px; font-size: clamp(0.9rem, 4vw, 1rem); border: none; cursor: pointer; margin-top: 2em; transition: background 0.3s ease;">Confirm Order</button>
                    </form>
                </div>

                <div class="summary" style="background: white; border-radius: 24px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02); padding: 2em;">
                    <h1 style="font-size: clamp(1.6rem, 5vw, 2rem); color: #1a237e; font-weight: 800; margin-bottom: 1em;">Order Summary</h1>
                    <div style="display: flex; flex-direction: column; gap: 0.75em;">
                        <p style="display: flex; justify-content: space-between; font-size: clamp(0.9rem, 4vw, 1rem); color: #64748b;">
                            <span>Cart Total</span>
                            <span>Ksh. {{ number_format($cart['subtotal'], 2) }}</span>
                        </p>
                        <p style="display: flex; justify-content: space-between; font-size: clamp(0.9rem, 4vw, 1rem); color: #64748b;">
                            <span>Shipping Cost</span>
                            <span id="shipping_cost_amount">0</span>
                        </p>
                        <p class="total" style="display: flex; justify-content: space-between; font-size: clamp(1rem, 4.5vw, 1.2rem); color: #1a237e; font-weight: 600;">
                            <span>Total</span>
                            <span id="total_amount">Ksh. {{ number_format($cart['subtotal'], 2) }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-slot name="javascript">
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                fbq('track', 'InitiateCheckout', {
                    value: {{ $cart['subtotal'] }}, // Using your cart subtotal
                    currency: 'KES',
                    num_items: {{ count($cart['items'] ?? []) }}, // Adjust based on your cart structure
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

                // Define areaPrice as a global variable
                let areaPrice = 0;

                // Function to update shipping cost and total
                function updateShippingAndTotal() {
                    const shippingCost = parseFloat(areaPrice); // Parse areaPrice as a float

                    if (!isNaN(shippingCost)) {
                        const cartSubtotal = parseFloat("{{ $cart['subtotal'] }}"); // Get cart subtotal from PHP as a float

                        // Format shipping cost and total with two decimal places
                        const formattedShippingCost = shippingCost.toFixed(2);
                        const formattedTotal = (shippingCost + cartSubtotal).toFixed(2);

                        // Update the DOM with formatted values
                        shippingCostElement.textContent = `Ksh. ${formattedShippingCost}`;
                        totalElement.textContent = `Ksh. ${formattedTotal}`;
                    } else {
                        console.error("Invalid shipping cost:", areaPrice);
                    }
                }

                locationSelect.addEventListener("change", function () {
                    const selectedLocationId = this.value;

                    // Make an Ajax request to fetch areas based on the selected location
                    fetch(`/areas/fetch/${selectedLocationId}`)
                        .then(response => response.json())
                        .then(data => {
                            // Clear and update the areas select box
                            areaSelect.innerHTML = "";
                            areaSelect.add(new Option("Select Area", ""));

                            data.forEach(area => {
                                areaSelect.add(new Option(area.area_name, area.id));
                            });
                        });
                });

                areaSelect.addEventListener("change", function () {
                    const selectedAreaId = this.value;

                    // Make an Ajax request to fetch the selected area's price
                    fetch(`/area/shipping-price/${selectedAreaId}`)
                        .then(response => response.json())
                        .then(data => {
                            areaPrice = data.price; // Update the global areaPrice variable

                            // Trigger update with the area's price
                            updateShippingAndTotal();
                        });
                });
            });
        </script>
    </x-slot>
</x-app-layout>
