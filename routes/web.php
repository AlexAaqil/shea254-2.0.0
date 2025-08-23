<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Pages\General\HomePage;
use App\Livewire\Pages\General\AboutPage;
use App\Livewire\Pages\General\ContactPage;

Route::get('/', HomePage::class)->name('home-page');
Route::get('about', AboutPage::class)->name('about-page');
Route::get('contact', ContactPage::class)->name('contact-page');
