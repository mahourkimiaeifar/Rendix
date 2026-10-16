<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;
use Livewire\Volt\Volt;

Volt::route('/', 'home-page');
Volt::route('/about', 'about-page');
Volt::route('/contact', 'contact-page');

// ... روت‌های فعلیت
Route::get('/sitemap.xml', [SitemapController::class, 'index']);