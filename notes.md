# TODOs
- google, meta, tiktok conversions APIs.
~~- wholesale price when quantity hits price tier quantity.~~
~~- stock management (CRUD).~~

- admin dashboard - sales amounts and locations filters.
- user dashboard - purchases.
- user dashboard - reviews.
- profile pictures for users.

- last 10 transactions with name and phone number.
- reward points (sh. 200 = 1 point).
- display reviews and ratings on products.
- count down timers when running offers.

- cashiers dashboard.

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
```