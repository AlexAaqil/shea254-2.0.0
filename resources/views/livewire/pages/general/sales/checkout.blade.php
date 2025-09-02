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
                    <form wire:submit.prevent="confirmOrder">
                        <div class="inputs_group">
                            <div class="inputs">
                                <label for="name">Full Name</label>
                                <input type="text" wire:model="name" id="name" placeholder="Enter your Full Name">
                                <x-form-input-error field="name" />
                            </div>

                            <div class="inputs">
                                <label for="email">Email Address</label>
                                <input type="email" wire:model="email" id="email" placeholder="example@gmail.com">
                                <x-form-input-error field="email" />
                            </div>
                        </div>

                        <div class="inputs">
                            <label for="phone_number">Phone Number (For payment)</label>
                            <input type="text" wire:model="phone_number" id="phone_number" placeholder="2547xxxxxxxx">
                            <x-form-input-error field="phone_number" />
                        </div>

                        <div class="inputs">
                            <label for="delivery_method">How would you like to receive your Order?</label>
                            <div class="custom_radio_buttons">
                                <label>
                                    <input class="option_radio" type="radio" wire:model="delivery_method" value="delivery">
                                    <span>Delivery</span>
                                </label>

                                <label>
                                    <input class="option_radio" type="radio" wire:model="delivery_method" value="pickup">
                                    <span>Pick it from the shop</span>
                                </label>
                            </div>
                            <x-form-input-error field="delivery_method" />
                        </div>

                        {{-- ✅ Only show if delivery is chosen --}}
                        @if($delivery_method === 'delivery')
                            <div class="inputs_group">
                                <div class="inputs">
                                    <label for="location">Location</label>
                                    <select wire:model="location" id="location">
                                        <option value="">Select Location</option>
                                        @foreach($locations as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <x-form-input-error field="location" />
                                </div>

                                <div class="inputs">
                                    <label for="area">Area</label>
                                    <select wire:model="area" id="area">
                                        <option value="">Select Area</option>
                                        @foreach($areas as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <x-form-input-error field="area" />
                                </div>
                            </div>

                            <div class="inputs">
                                <label for="address">Address</label>
                                <input type="text" wire:model="address" id="address" placeholder="Enter delivery address">
                                <x-form-input-error field="address" />
                            </div>

                            <div class="inputs">
                                <label for="additional_information">Additional Information</label>
                                <input type="text" wire:model="additional_information" id="additional_information" placeholder="Apartment name, directions, etc.">
                                <x-form-input-error field="additional_information" />
                            </div>
                        @endif

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
                    <span>Ksh. {{ number_format($shipping_cost, 2) }}</span>
                </div>

                <div class="summary_item">
                    <span>Total Amount:</span>
                    <span>Ksh. {{ number_format($total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </section>
</div>
