<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Pages\General\HomePage;

Route::get('/', HomePage::class)->name('home-page');
