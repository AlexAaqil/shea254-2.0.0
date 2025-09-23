<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Pages\General\HomePage;
use App\Livewire\Pages\General\AboutPage;
use App\Livewire\Pages\General\Products\Shop as ShopPage;
use App\Livewire\Pages\General\Products\Details as ProductDetailsPage;
use App\Livewire\Pages\General\Products\Categorized as ProductsCategorizedPage;
use App\Livewire\Pages\General\ContactPage;
use App\Livewire\Pages\General\Sales\Cart as CartPage;
use App\Http\Controllers\Sales\SaleController;
use App\Livewire\Pages\General\Blogs\Index as UsersBlogsPage;
use App\Livewire\Pages\General\Blogs\Details as UsersBlogsDetailsPage;

use App\Livewire\Pages\Dashboards\Index as Dashboard;
use App\Http\Controllers\Products\ProductReviewController;

use App\Livewire\Pages\Users\Index as UsersPage;
use App\Http\Controllers\UserController;
use App\Livewire\Pages\Products\Products\Index as ProductsPage;
use App\Http\Controllers\Products\ProductController;
use App\Livewire\Pages\Products\Categories\Index as ProductCategoriesPage;
use App\Http\Controllers\Products\ProductCategoryController;
use App\Livewire\Pages\Products\Measurements\Index as ProductMeasurementsPage;
use App\Http\Controllers\Products\ProductMeasurementController;
use App\Livewire\Pages\Products\Reviews\Index as ProductReviews;
use App\Livewire\Pages\Products\Offers\Index as ProductOffers;
use App\Livewire\Pages\Deliveries\Locations\Index as DeliveryLocationsPage;
use App\Http\Controllers\Deliveries\DeliveryLocationController;
use App\Livewire\Pages\Deliveries\Areas\Index as DeliveryAreasPage;
use App\Http\Controllers\Deliveries\DeliveryAreaController;
use App\Livewire\Pages\Orders\Index as OrdersPage;
use App\Livewire\Pages\Orders\Edit as EditOrder;
use App\Livewire\Pages\Blogs\Blogs\Index as BlogsPage;
use App\Http\Controllers\Blogs\BlogController;
use App\Livewire\Pages\Blogs\Categories\Index as BlogCategoriesPage;
use App\Http\Controllers\Blogs\BlogCategoryController;
use App\Livewire\Pages\ContactMessages\Index as ContactMessagesPage;
use App\Livewire\Pages\ContactMessages\Edit as EditContactMessage;


Route::get('/', HomePage::class)->name('home-page');
Route::get('about', AboutPage::class)->name('about-page');
Route::get('shop', ShopPage::class)->name('shop-page');
Route::get('products/details/{slug}', ProductDetailsPage::class)->name('product-details-page');
Route::get('products/categorized/{category}', ProductsCategorizedPage::class)->name('products-categorized-page');
Route::get('contact', ContactPage::class)->name('contact-page');
Route::get('cart', CartPage::class)->name('cart-page');
Route::get('checkout', [SaleController::class, 'checkout'])->name('checkout-page');
Route::post('checkout', [SaleController::class, 'store'])->name('checkout.store');
Route::get('order-successful', [SaleController::class, 'success'])->name('checkout.success');
Route::get('areas/fetch/{location}', [DeliveryAreaController::class, 'areasFetch'])->name('areas.fetch');
Route::get('areas/shipping-cost/{area}', [DeliveryAreaController::class, 'areasShippingCost'])->name('areas.shipping-cost');
Route::get('blog', UsersBlogsPage::class)->name('users-blogs-page');
Route::get('blog/{blog}', UsersBlogsDetailsPage::class)->name('users-blogs-details-page');

Route::middleware(['authenticated_user'])->group(function() {
    Route::get('dashboard', Dashboard::class)->name('dashboard');

    Route::get('product-reviews/{product}', [ProductReviewController::class, 'create'])->name('product-reviews.create');
    Route::post('product-reviews/{product}', [ProductReviewController::class, 'store'])->name('product-reviews.store');
});

Route::middleware(['admin_only'])->group(function() {
    Route::get('users', UsersPage::class)->name('users.index');
    Route::resource('users', UserController::class)->only(['create', 'store', 'edit', 'update']);

    Route::get('products', ProductsPage::class)->name('products.index');
    Route::resource('products', ProductController::class)->only(['create', 'store', 'edit', 'update']);

    Route::get('product-categories', ProductCategoriesPage::class)->name('product-categories.index');
    Route::resource('product-categories', ProductCategoryController::class)->only(['create', 'store', 'edit', 'update']);

    Route::get('product-measurements', ProductMeasurementsPage::class)->name('product-measurements.index');
    Route::resource('product-measurements', ProductMeasurementController::class)->only(['create', 'store', 'edit', 'update']);

    Route::get('product-reviews', ProductReviews::class)->name('product-reviews.index');
    Route::resource('product-reviews', ProductReviewController::class)->only(['edit', 'update']);

    Route::get('product-offers', ProductOffers::class)->name('product-offers.index');

    Route::get('delivery-locations', DeliveryLocationsPage::class)->name('delivery-locations.index');
    Route::resource('delivery-locations', DeliveryLocationController::class)->only(['create', 'store', 'edit', 'update']);

    Route::get('delivery-areas', DeliveryAreasPage::class)->name('delivery-areas.index');
    Route::resource('delivery-areas', DeliveryAreaController::class)->only(['create', 'store', 'edit', 'update']);

    Route::get('orders', OrdersPage::class)->name('orders.index');
    Route::get('orders/{order}/edit', EditOrder::class)->name('orders.edit');
    Route::post('orders/{order}/retry-payment', [SaleController::class, 'requestSTKPush'])->name('orders.request_stkpush');

    Route::get('blogs', BlogsPage::class)->name('blogs.index');
    Route::resource('blogs', BlogController::class)->only(['create', 'store', 'edit', 'update']);

    Route::get('blog-categories', BlogCategoriesPage::class)->name('blog-categories.index');
    Route::resource('blog-categories', BlogCategoryController::class)->only(['create', 'store', 'edit', 'update']);

    Route::get('contact-messages', ContactMessagesPage::class)->name('contact-messages.index');
    Route::get('contact-messages/{message}/edit', EditContactMessage::class)->name('contact-messages.edit');
});

require __DIR__ . '/auth.php';
