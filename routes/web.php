<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'pages.home-page')->name('home');
Volt::route('/about', 'pages.about-page')->name('about');
Volt::route('/contact', 'pages.contact-page')->name('contact');

Route::prefix('admin')->name('admin.')->group(function () {
    Volt::route('/login', 'admin.auth.login-page')->name('login');

    Route::middleware(['admin.auth'])->group(function () {
        Volt::route('/', 'admin.dashboard-page')->name('dashboard');
        Volt::route('/leads', 'admin.leads.index-page')->name('leads.index');

        Route::post('/logout', function () {
            Auth::logout();

            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('admin.login');
        })->name('logout');
    });
});