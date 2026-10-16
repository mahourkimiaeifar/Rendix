<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'home-page');
Volt::route('/about', 'about-page');
Volt::route('/contact', 'contact-page');