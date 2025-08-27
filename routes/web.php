<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Pages\General\HomePage;
use App\Livewire\Pages\General\AboutPage;
use App\Livewire\Pages\General\Products\Shop as ShopPage;
use App\Livewire\Pages\General\Products\Details as ProductDetailsPage;
use App\Livewire\Pages\General\ContactPage;

use App\Livewire\Pages\Dashboards\Index as Dashboard;

use App\Livewire\Pages\Users\Index as UsersPage;
use App\Livewire\Pages\Users\Form as CreateUser;
use App\Livewire\Pages\Users\Form as EditUser;

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
    Route::get('users/create', CreateUser::class)->name('users.create');
    Route::get('users/{id}/edit', EditUser::class)->name('users.edit');
});

require __DIR__ . '/auth.php';
