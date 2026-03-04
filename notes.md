# TODOs
- add options for delivery using country, zip code.
- add payments: mastercard, paypal, visa, amex, discover.

- google, meta, tiktok conversions APIs.
~~- wholesale price when quantity hits price tier quantity.~~
~~- stock management (CRUD).~~

- admin dashboard - sales amounts and locations filters.
- profile pictures for users.
~~- user dashboard - purchases.~~
~~- user dashboard - reviews.~~

~~- last 10 transactions with name and phone number.~~
- reward points (sh. 200 = 1 point).
- display reviews and ratings on products.
- count down timers when running offers.

~~- cashiers dashboard.~~

===========================================

===========================================

# Payment Methods
SasaPay Network codes for payment:
- 63902 (MPesa)
- 63903 (AirtelMoney) 
- 63907 (T-Kash)



# DB Design
```
users {
    id();
    string('first_name');
    string('last_name');
    string('email')->unique();
    timestamp('email_verified_at')->nullable();
    string('phone_number');
    unsignedTinyInteger('user_level')->default(2);
    boolean('user_status')->default(1);
    string('password');

    rememberToken();
    timestamps();
}

comments {
    id();
    string('full_name');
    string('email');
    string('phone_number');
    string('message');
    timestamps();
}

sales {
    id();
    string('order_number');
    boolean('order_type');
    string('discount_code')->nullable();
    decimal('discount',10,2)->default(0.00);
    decimal('total_amount', 10,2)->default(0.00);
    decimal('amount_paid', 10,2)->default(0.00);
    string('payment_method')->nullable();
    
    foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
    timestamps();
}

blog_categories {
    id();
    string('title')->unique();
    string('slug')->index();
    timestamps();
}

blogs {
    id();
    string('image')->nullable();
    string('title')->unique();
    string('slug')->index();
    text('content');
    foreignId('category_id')->nullable()->constrained('blog_categories')->onDelete('set null');
    timestamps();
}

delivery_locations {
    id();
    string('location_name')->unique();
    timestamps();
}

delivery_areas {
    id();
    foreignId('delivery_location_id')->constrained('delivery_locations')->onDelete('cascade');
    string('area_name')->unique();
    decimal('price', 10, 2)->default(0.00);
    timestamps();
}

product_measurements {
    id();
    string('measurement_name');
    timestamps();
}

product_categories {
    id();
    string('title')->unique();
    string('slug')->index();
    timestamps();
}

products {
    id();
    string('title')->unique();
    string('slug')->index();
    unsignedSmallInteger('product_code')->default(0);
    boolean('featured')->default(0);
    boolean('is_visible')->default(1);
    decimal('buying_price', 10, 2)->default(0.00);
    decimal('selling_price', 10, 2)->default(0.00);
    decimal('discount_price', 10, 2)->default(0.00)->nullable();
    unsignedSmallInteger('product_measurement')->nullable();
    unsignedSmallInteger('product_order')->default(200);
    unsignedSmallInteger('stock_count')->default(0);
    unsignedSmallInteger('safety_stock')->default(0);
    text('description')->nullable();
    foreignId('category_id')->nullable()->constrained('product_categories')->onDelete('set null');
    foreignId('measurement_id')->nullable()->constrained('product_measurements')->onDelete('set null');
    timestamps();
}

product_images {
    id();
    foreignId('product_id')->constrained('products');
    string('image');
    smallInteger('image_order')->default(5);
    timestamps();
}

sales {
    id();
    string('order_number');
    boolean('order_type');
    string('discount_code')->nullable();
    decimal('discount',10,2)->default(0.00);
    decimal('total_amount', 10,2)->default(0.00);
    decimal('amount_paid', 10,2)->default(0.00);
    string('payment_method')->nullable();
    foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
    timestamps();

    string('status')->nullable()->after('payment_method');
    index('status');
}

order_items {
    id();
    foreignId('order_id')->constrained('sales')->onDelete('cascade');
    foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
    string('title');
    unsignedSmallInteger('quantity')->default(1);
    decimal('buying_price',10,2)->default(0);
    decimal('selling_price',10,2)->default(0);
    timestamps();
}

order_deliveries {
    id();
    foreignId('order_id')->constrained('sales')->onDelete('cascade');
    string('full_name');
    string('email');
    string('phone_number');
    string('address');
    string('additional_information')->nullable();
    string('location');
    string('area');
    decimal('shipping_cost');
    string('delivery_status')->default('pending');
    timestamps();
}

product_reviews {
    id();
    foreignId('product_id')->constrained('products')->onDelete('cascade');
    foreignId('user_id')->constrained('users')->onDelete('cascade');
    unsignedTinyInteger('rating');
    string('review', 1500);
    string('image')->nullable();
    boolean('is_visible')->default(1);
    unsignedMediumInteger('ordering')->default(100);
    timestamps();
}

payments {
    id();
    string('status');
    string('payment_gateway');
    string('merchant_request_id');
    string('checkout_request_id');
    string('transaction_reference');
    string('response_code');
    string('response_description');
    text('customer_message');
    foreignId('order_id')->constrained('sales')->onDelete('cascade');
    timestamps();
}

product_price_tiers {
    id();
    unsignedInteger('min_quantity');
    decimal('price', 10, 2);

    foreignId('product_id')->constrained('products')->onDelete('cascade');
    timestamps();

    unique(['product_id', 'min_quantity']);
}
```