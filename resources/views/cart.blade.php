<x-app-layout>
    @include('partials.navbar')

    <main class="Cart" style="background: linear-gradient(135deg, #f8fafc, #ffffff); padding: 4em 1em;">
        @include('partials.messages')

        <div class="container" style="max-width: 1200px; margin: 0 auto;">
            <div class="header" style="text-align: center; margin-bottom: 3em;">
                <h1 style="font-size: clamp(1.8rem, 5vw, 2.5rem); color: #1a237e; font-weight: 800;">Your Cart</h1>
                <p style="color: #64748b; font-size: clamp(0.9rem, 3.5vw, 1.1rem);">You have {{ Session::get('cart_count', 0) }} items in your cart</p>
            </div>

            <div class="body" style="background: white; border-radius: 24px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02); padding: 2em;">
                <ul class="list_style_none" style="list-style: none; padding: 0;">
                    @foreach($cart['items'] as $product)
                    <li style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); align-items: center; gap: 1em; padding: 1em 0; border-bottom: 1px solid #e2e8f0;">
                        <span class="title" style="font-size: clamp(0.9rem, 4vw, 1.1rem); font-weight: 600;">
                            <a href="{{ route('products.details', $product['slug']) }}" style="color: #1a237e; text-decoration: none; transition: color 0.3s ease;">
                                {{ $product['title'] }}
                            </a>
                        </span>

                        <span class="price" style="font-size: clamp(0.9rem, 4vw, 1.1rem); color: #1a237e; font-weight: 600;">
                            <span class="currency">Ksh. </span>
                            <span class="price_amount">{{ $product['selling_price'] }}</span>
                            @if($product['quantity'] > 1 && $product['selling_price'] < $product['discount_price'])
                                <small style="display: block; font-size: 0.8em; color: #4CAF50;">
                                    (Wholesale price for {{ $product['quantity'] }} items)
                                </small>
                            @elseif($product['discount_price'] < $product['selling_price'])
                                <small style="display: block; font-size: 0.8em; color: #4CAF50;">
                                    (Discount price)
                                </small>
                            @endif
                        </span>

                        <span class="product_quantity" style="display: flex; align-items: center; gap: 0.5em; justify-content: center;">
                            <form class="quantity_form" action="{{ route('change_quantity', $product['id']) }}" method="post" style="display: flex; align-items: center;">
                                @csrf
                                <input type="number" name="quantity" class="quantity_input" min="1" value="{{ $product['quantity'] }}" style="width: 60px; padding: 0.5em; font-size: clamp(0.9rem, 4vw, 1rem); border: 1px solid #e2e8f0; border-radius: 8px; text-align: center;">
                            </form>
                        </span>

                        <span class="subtotal" style="font-size: clamp(0.9rem, 4vw, 1.1rem); color: #1a237e; font-weight: 600; justify-self: end;">
                            <span class="currency">Ksh. </span>
                            <span class="subtotal_amount">{{ $product['quantity'] * $product['selling_price'] }}</span>
                        </span>

                        <span class="delete_from_cart" style="justify-self: end;">
                            <form id="deleteForm_{{ $product['id'] }}" action="{{ route('cart.destroy', $product['id']) }}" method="post">
                                @csrf
                                @method('DELETE')

                                <button type="button" onclick="deleteItem({{ $product['id'] }}, 'product');" style="background: none; border: none; cursor: pointer;">
                                    <i class="fas fa-trash-alt text-danger" style="font-size: clamp(1rem, 4vw, 1.2rem);"></i>
                                </button>
                            </form>
                        </span>
                    </li>
                    @endforeach
                </ul>

                <div class="summary" style="margin-top: 3em;">
                    <h1 style="font-size: clamp(1.6rem, 5vw, 2rem); color: #1a237e; font-weight: 800; margin-bottom: 1em;">Order Summary</h1>
                    <p style="display: flex; justify-content: space-between; font-size: clamp(0.9rem, 4vw, 1.1rem); color: #64748b; margin-bottom: 1.5em;">
                        <span>Cart Total</span>
                        <span id="cart_total" style="color: #1a237e; font-weight: 600;">Ksh. {{ $cart['subtotal'] }}</span>
                    </p>

                    <div class="" style="text-align: right;">
                        <a href="{{ route('checkout.create') }}" style="display: inline-block; background: #1a237e; color: white; text-decoration: none; padding: 0.75em 1.5em; border-radius: 24px; font-size: clamp(0.9rem, 4vw, 1rem); transition: background 0.3s ease;">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <x-slot name="javascript">
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let quantityInputs = document.querySelectorAll('.quantity_input');

                quantityInputs.forEach(function(input) {
                    input.addEventListener('change', function() {
                        let form = this.closest('.quantity_form');
                        let formData = new FormData(form);

                        fetch(form.action, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            if (response.ok) {
                                // If form submission is successful, refresh the page
                                location.reload();
                            } else {
                                console.error('Form submission failed:', response.status);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                    });
                });
            });
        </script>
        <x-sweetalert></x-sweetalert>
    </x-slot>

</x-app-layout>
