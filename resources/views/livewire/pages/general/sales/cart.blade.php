<div class="CartPage">
    <section class="Hero">
        <div class="container">
            <h1>Your Cart</h1>
            <p>You have {{ $cart_count }} {{ Str::plural('item', $cart_count) }} in your cart</p>
        </div>
    </section>

    <section class="CartItems">
        <div class="container">
            <div class="cart_items">
                @foreach($cart_items as $item)
                    <div class="cart_item">
                        <div class="product">
                            <span>{{ $item->product->title }}</span>
                        </div>

                        <div class="price">
                            <span>{{ number_format($item->product->effective_price, 2) }}</span>
                        </div>

                        <div class="quantity">
                            <button class="qty_btn" wire:click="decreaseQuantity({{ $item->product->id }})"  @if($item->quantity <= 1) disabled @endif>-</button>
                            <span class="qty">{{ $item->quantity }}</span>
                            <button class="qty_btn" wire:click="increaseQuantity({{ $item->product->id }})">+</button>
                        </div>

                        <div class="sub_total">
                            <span class="total">{{ number_format($item->product->effective_price * $item->quantity, 2) }}</span>
                        </div>

                        <div class="cart_actions">
                            <button wire:click="removeItem({{ $item->product->id }})" class="btn">
                                <x-svgs.trash />
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="summary">
                <h2>Cart Summary</h2>

                <div class="summary_item">
                    <span>Total Items:</span>
                    <span>{{ $cart_count }}</span>
                </div>

                <div class="summary_item">
                    <span>Cart Total:</span>
                    <span>Ksh. {{ number_format($cart_items->sum(function($item) {
                        return $item->product->effective_price * $item->quantity;
                    }), 2) }}</span>
                </div>

                <div class="summary_actions">
                    @if($cart_count != 0)
                        <a href="{{ Route::has('checkout-page') ? route('checkout-page') : '#' }}" class="btn">Proceed to Checkout</a>
                    @else
                        <a href="{{ Route::has('shop-page') ? route('shop-page') : '#' }}" class="btn">Add Items to Cart</a>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
