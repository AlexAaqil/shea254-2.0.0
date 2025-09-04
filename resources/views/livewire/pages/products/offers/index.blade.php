<div class="ProductOffers">
    <div class="container">
        <div class="app_header">
            <div class="info">
                <h2>Products Offers</h2>
                <div class="stats">
                    <span>{{ $count_products }} {{ Str::plural('product', $count_products) }}</span>
                </div>
            </div>

            <div class="search">
                <!-- TODO: Add the search functionality -->
            </div>

            <div class="button" style="display: flex; gap: 10px;">
                <button
                    type="button"
                    wire:click="removeAllDiscounts"
                    wire:loading.attr="disabled"
                    wire:target="removeAllDiscounts"
                    class="btn"
                    style="background: #dc3545;"
                >
                    <span wire:loading.remove wire:target="removeAllDiscounts">
                        Remove Discounts
                    </span>
                    <span wire:loading wire:target="removeAllDiscounts">
                        Removing Discounts...
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="saveDiscounts"
                    wire:loading.attr="disabled"
                    wire:target="saveDiscounts"
                    class="btn"
                >
                    <span wire:loading.remove wire:target="saveDiscounts">
                        Save Discounts
                    </span>
                    <span wire:loading wire:target="saveDiscounts">
                        Saving Discounts...
                    </span>
                </button>
            </div>
        </div>

        <div class="orders_list">
            @if($count_products)
                <div class="table">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Buying Price</th>
                                <th>Selling Price</th>
                                <th>Discount Price</th>
                                <th>Discount %</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($products as $index => $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $product->title }}</td>
                                    <td>{{ number_format($product->buying_price, 2) }}</td>
                                    <td>{{ number_format($product->selling_price, 2) }}</td>
                                    <td>
                                        <input
                                            type="number"
                                            wire:model="discounts.{{ $product->id }}"
                                            step="0.01"
                                            min="0"
                                            max="{{ $product->selling_price }}"
                                            placeholder="Enter discount"
                                            wire:loading.attr="disabled"
                                        >
                                        @error('discounts.' . $product->id)
                                            <span class="text-danger text-sm">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="text-center">
                                        @if(isset($discounts[$product->id]) && is_numeric($discounts[$product->id]) && $discounts[$product->id] != $product->selling_price)
                                            {{ number_format((($product->selling_price - $discounts[$product->id]) / $product->selling_price) * 100) }}%
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    @if($search_performed)
                        No products found for "{{ $search }}"
                    @else
                        No products available
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
