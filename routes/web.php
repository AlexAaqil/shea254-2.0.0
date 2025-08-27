<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Pages\General\HomePage;
use App\Livewire\Pages\General\AboutPage;
use App\Livewire\Pages\General\Products\Shop as ShopPage;
use App\Livewire\Pages\General\Products\Details as ProductDetailsPage;
use App\Livewire\Pages\General\ContactPage;

use App\Livewire\Pages\Dashboards\Index as Dashboard;

use App\Livewire\Pages\Users\Index as UsersPage;
use App\Http\Controllers\UserController;
use App\Livewire\Pages\Products\Products\Index as ProductsPage;
use App\Http\Controllers\Products\ProductController;
use App\Livewire\Pages\Products\Categories\Index as ProductCategoriesPage;
use App\Http\Controllers\Products\ProductCategoryController;
use App\Livewire\Pages\Products\Measurements\Index as ProductMeasurementsPage;
use App\Http\Controllers\Products\ProductMeasurementController;

Route::get('/', HomePage::class)->name('home-page');
Route::get('about', AboutPage::class)->name('about-page');
Route::get('shop', ShopPage::class)->name('shop-page');
Route::get('products/details/{slug}', ProductDetailsPage::class)->name('product-details-page');
Route::get('contact', ContactPage::class)->name('contact-page');

Route::middleware(['authenticated_user'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
});

Route::middleware(['admin_only'])->group(function () {
    Route::get('users', UsersPage::class)->name('users.index');
    Route::resource('users', UserController::class)->only(['create', 'store', 'edit', 'update']);

    Route::get('products', ProductsPage::class)->name('products.index');
    Route::resource('products', ProductController::class)->only(['create', 'store', 'edit', 'update']);

    Route::get('product-categories', ProductCategoriesPage::class)->name('product-categories.index');
    Route::resource('product-categories', ProductCategoryController::class)->only(['create', 'store', 'edit', 'update']);

    Route::get('product-measurements', ProductMeasurementsPage::class)->name('product-measurements.index');
    Route::resource('product-measurements', ProductMeasurementController::class)->only(['create', 'store', 'edit', 'update']);
});

require __DIR__ . '/auth.php';
