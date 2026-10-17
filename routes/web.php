<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;
use Livewire\Volt\Volt;

Volt::route('/', 'pages.home-page')->name('home');
Volt::route('/about', 'pages.about-page')->name('about');
Volt::route('/contact', 'pages.contact-page')->name('contact');

// ... روت‌های فعلیت
Route::get('/sitemap.xml', [SitemapController::class, 'index']);