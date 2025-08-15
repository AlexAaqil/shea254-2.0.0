# Features
- Authentication and Role Based Authorization.
- User Managment by Admins.
- Contact Form Messages.



# User Stories
- users can register an account.
- registered users can login.
- admins can update user roles to: admin, user.
- super admins can update user roles to: super admin, owner, admin, user.
- super admins can delete users.

- users can send contact form messages.
- admins can read contact form messages.



# DB Design
```
users {
    id();
    uuid('uuid')->unique();
    string('first_name');
    string('last_name');
    string('email');
    string('phone_number');
    unsignedTinyInteger('role')->default(2);
    boolean('status')->default(true);
    string('image')->nullable();
    timestamp('email_verified_at')->nullable();
    string('password');
    timestamp('last_login_at')->nullable();
    string('last_login_ip')->nullable();
    rememberToken();
    timestamps();
    softDeletes();
}

contact_messages {
    id();
    uuid('uuid')->unique();
    string('name');
    string('email');
    string('phone_number');
    string('message', 2000);
    string('response', 2000)->nullable();
    string('notes')->nullable();
    boolean('is_read')->default(0);
    boolean('is_important')->default(0);
    timestamps();
    softDeletes();
}

delivery_regions {
    id();
    uuid('uuid')->unique();
    string('name')->unique();
    string('slug')->unique();
    string('country')->default('KE');
    timestamps();
}

delivery_areas {
    id();
    uuid('uuid')->unique();
    string('name')->unique();
    string('slug')->unique();
    decimal('delivery_fee', 10, 2)->default(0.00);
    string('delivery_time')->nullable();
    string('postal_code')->nullable();
    json('coordinates')->nullable();

    foreignId('delivery_region_id')->constrained('delivery_regions')->cascadeOnDelete();
    timestamps();
}

product_categories {
    id();
    uuid('uuid')->unique();
    string('title')->unique();
    string('slug')->unique()->index();
    string('description')->nullable();
    string('image')->nullable();
    timestamps();
}

products {
    id();
    uuid('uuid')->unique();
    string('title')->unique();
    string('slug')->unique();
    string('product_code')->nullable();
    string('sku')->unique();
    string('barcode')->nullable();
    boolean('is_featured')->default(false);
    boolean('is_visible')->default(true);
    decimal('production_cost', 10, 2)->default(0.00);
    decimal('selling_price', 10, 2)->default(0.00);
    decimal('discount_price', 10, 2)->default(0.00)->nullable();
    unsignedTinyInteger('discount_percentage')->default(0)->nullable();
    decimal('weight')->nullable();
    string('weight_unit')->nullable();
    boolean('track_inventory')->default(true);
    unsignedSmallInteger('stock_count')->default(0)->nullable();
    unsignedSmallInteger('safety_stock')->default(0)->nullable();
    text('description')->nullable();
    json('attributes')->nullable();
    unsignedSmallInteger('sort_order')->default(0);
    string('meta_title')->nullable();
    string('meta_description', 500)->nullable();
    string('canonical_url')->nullable();
    json('meta_tags')->nullable();
    json('og_tags')->nullable();
    boolean('noindex')->default(false);
    boolean('nofollow')->default(false);

    foreignId('product_category_id')->nullable()->constrained('product_categories')->nullOnDelete();
    timestamps();

    index(['is_visible', 'is_featured']);
    index('product_category_id');
}

product_price_tiers {
    id();
    uuid('uuid')->unique();
    unsignedTinyInteger('min_quantity');
    decimal('price', 10, 2);
    unsignedTinyInteger('customer_type')->default(0)->index()->comment('0 => retail, 1 => wholesale, 2 => vip, 3 => b2b');
    string('region')->nullable()->index()->comment('For regional pricing like: KE, US');
    boolean('is_active')->default(false);
    date('starts_at')->nullable();
    date('ends_at')->nullable();

    foreignId('product_id')->constrained('products')->cascadeOnDelete();
    timestamps();
}

product_images {
    id();
    uuid('uuid')->unique();
    string('image');
    unsignedSmallInteger('sort_order')->default(5);

    foreignId('product_id')->constrained('products')->cascadeOnDelete();
    timestamps();
}

product_views {
    id();
    uuid('uuid')->unique();
    foreignId('product_id')->constrained()->onDelete('cascade');
    foreignId('user_id')->nullable()->constrained('users');
    string('ip_address')->nullable();
    string('user_agent')->nullable();
    timestamp('viewed_at');
}

product_reviews {
    id();
    uuid('uuid')->unique();
    unsignedTinyInteger('rating');
    string('review', 1500);
    string('image')->nullable();
    boolean('is_visible')->default(1);
    unsignedMediumInteger('sort_order')->default(0);

    foreignId('user_id')->constrained('users')->cascadeOnDelete();
    foreignId('product_id')->constrained('products')->cascadeOnDelete();
    timestamps();
}

product_discounts {
    id();
    uuid('uuid')->unique();
    decimal('amount')->default(0.00);
    unsignedTinyInteger('percentage')->nullable()->default(0);
    timestamp('starts_at')->nullable();
    timestamp('ends_at')->nullable();

    foreignId('product_id')->constrained('products')->cascadeOnDelete();
    timestamps();
}

discount_codes {
    id();
    uuid('uuid')->unique();
    string('code')->unique();
    unsignedTinyInteger('type');
    decimal('value', 10, 2);
    boolean('is_active')->default(true);
    integer('usage_limit')->nullable();
    timestamp('starts_at')->nullable();
    timestamp('ends_at')->nullable();
    string('applies_to')->nullable();
    json('conditions')->nullable();
    timestamps();
}

orders {
    id();
    uuid('uuid')->unique();
    string('order_number');
    unsignedTinyInteger('order_status')->default(0);
    unsignedTinyInteger('sale_type');
    decimal('production_cost', 10, 2)->default(0.00);
    decimal('total_amount', 10, 2)->default(0.00);
    decimal('amount_paid', 10, 2)->default(0.00);
    unsignedTinyInteger('payment_method')->nullable();
    string('currency')->nullable()->comment('USD, KES');
    decimal('tax_amount', 10, 2)->default(0.00);
    string('ip_address')->nullable();
    string('user_agent')->nullable();

    foreignId('discount_code_id')->nullable()->constrained('discount_codes')->nullOnDelete();
    foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
    timestamps();

    index(['user_id', 'order_status']);

    $table->softDeletes();
}

order_items {
    id();
    uuid('uuid')->unique();
    string('title');
    unsignedSmallInteger('quantity')->default(1);
    decimal('production_cost',10,2)->default(0);
    decimal('selling_price',10,2)->default(0);

    foreignId('order_id')->constrained('orders')->cascadeOnDelete();
    foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
    timestamps();
}

order_deliveries {
    id();
    uuid('uuid')->unique();
    string('name');
    string('email');
    string('phone_number');
    string('address');
    string('additional_information')->nullable();
    string('region');
    string('area');
    decimal('shipping_cost');
    unsignedTinyInteger('delivery_status')->default(0);

    foreignId('order_id')->constrained('orders')->cascadeOnDelete();
    timestamps();

    $table->softDeletes();
}

payments {
    id();
    uuid('uuid')->unique();
    unsignedTinyInteger('status')->default(0);
    unsignedTinyInteger('payment_method')->nullable();
    unsignedTinyInteger('response_code')->nullable();
    decimal('amount', 10, 2)->nullable();
    string('currency')->nullable();
    string('response_description')->nullable();
    string('merchant_request_id')->nullable();
    string('checkout_request_id')->nullable();
    string('transaction_reference')->nullable();
    text('customer_message');
    timestamp('paid_at')->nullable();

    foreignId('order_id')->constrained('orders')->cascadeOnDelete();
    timestamps();
}

blog_categories {
    id();
    uuid('uuid')->unique();
    string('title')->unique();
    string('slug')->unique();
    string('description')->nullable();
    string('image')->nullable();
    timestamps();
}

blogs {
    id();
    uuid('uuid')->unique();
    string('title')->unique();
    string('slug')->unique();
    text('content')->nullable();
    text('excerpt')->nullable(); // Short summary for cards, lists, SEO
    string('image')->nullable();
    json('tags')->nullable();

    $table->boolean('is_featured')->default(false);
    boolean('is_published')->default(true);
    dateTime('published_at')->nullable();
    boolean('noindex')->default(false);
    boolean('nofollow')->default(false);

    $table->unsignedInteger('reading_time')->nullable();
    unsignedInteger('word_count')->nullable();

    $table->string('meta_title')->nullable(); // < 60 chars
    string('meta_description', 500)->nullable(); // < 155 chars
    string('meta_keywords')->nullable();
    string('canonical_url')->nullable();
    json('meta_tags')->nullable();
    json('og_tags')->nullable();
    json('structured_data')->nullable(); // to store dynamic BlogPosting schema or other types, then render directly in Blade views.

    foreignId('blog_category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
    foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
    timestamps();
}

blog_comments {
    id();
    uuid('uuid')->unique();
    text('content');
    boolean('is_visible')->default(true);

    foreignId('user_id')->constrained('users')->cascadeOnDelete();
    foreignId('blog_id')->constrained('blogs')->cascadeOnDelete();
    timestamps();
}
```



# Enums
```
USER_ROLES = [
    SUPER_ADMIN = 0;
    ADMIN = 1;
    OWNER = 2;
    USER = 3;
]

SALES_TYPES = [
    0 = 'Online';
    1 = 'POS';
]

DELIVERY_STATUSES = [
    0 = 'Pending';
    1 = 'Shipped';
    2 = 'Delivered';
]

ORDER_STATUSES = [
    0 = 'Pending';
    1 = 'Delivered';
    2 = 'Cancelled';
]

DISCOUNT_TYPES = [
    0 = 'Amount';
    1 = 'Percentage';
]

PAYMENT_METHODS = [
    0 = 'KCB Mpesa Express';
    1 = 'Cash';
]

PAYMENT_STATUSES = [
    0 = 'Pending';
    1 = 'Paid';
    2 = 'Cancelled';
    3 = 'Failed';
    4 = 'Partially Paid';
]

PAYMENT_RESPONSE_CODES = [
    0 = 'Pending';
    1 = 'Success';
    2 = 'Failed';
]
```
