<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div id="rendix-navigation-root">
    <aside class="rx-sidebar" aria-label="ناوبری اصلی">
        <a href="/" wire:navigate class="rx-logo-link" aria-label="صفحه اصلی رندیکس">
            <img src="/images/logo.png?v=5" alt="لوگوی رندیکس" width="170" height="64" loading="eager" decoding="async"
                class="rx-logo-img h-14 w-auto">
            <span class="sr-only">رندیکس</span>
        </a>

        <nav class="rx-side-nav">
            <a href="/" wire:navigate data-nav-link class="rx-side-link {{ request()->is('/') ? 'is-active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span>خانه</span>
            </a>

            <a href="/about" wire:navigate data-nav-link
                class="rx-side-link {{ request()->is('about') ? 'is-active' : '' }}">
                <i class="fa-solid fa-user-group"></i>
                <span>درباره</span>
            </a>

            <a href="/contact" wire:navigate data-nav-link
                class="rx-side-link {{ request()->is('contact') ? 'is-active' : '' }}">
                <i class="fa-solid fa-paper-plane"></i>
                <span>تماس</span>
            </a>
        </nav>

        <div class="rx-side-actions">
            <button type="button" data-rendix-theme-toggle class="rx-icon-btn" aria-label="تغییر تم">
                <i class="fa-solid fa-moon rx-theme-dark"></i>
                <i class="fa-solid fa-sun rx-theme-light"></i>
            </button>

            <a href="https://t.me/RendixDMServices" target="_blank" rel="noopener noreferrer" class="rx-icon-btn"
                aria-label="کانال تلگرام رندیکس">
                <i class="fa-brands fa-telegram"></i>
            </a>

            <a href="/contact" wire:navigate class="rx-icon-btn" aria-label="شروع پروژه">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    </aside>

    <header class="rx-mobile-nav">
        <a href="/" wire:navigate class="rx-logo-link" aria-label="صفحه اصلی رندیکس">
            <img src="/images/logo.png?v=5" alt="لوگوی رندیکس" width="150" height="56" loading="eager" decoding="async"
                class="rx-logo-img h-12 w-auto">
            <span class="sr-only">رندیکس</span>
        </a>

        <div class="flex items-center gap-2">
            <button type="button" data-rendix-theme-toggle class="rx-icon-btn" aria-label="تغییر تم">
                <i class="fa-solid fa-moon rx-theme-dark"></i>
                <i class="fa-solid fa-sun rx-theme-light"></i>
            </button>

            <button id="mobile-menu-button" type="button" data-mobile-menu-button class="rx-icon-btn"
                aria-label="باز کردن منو" aria-controls="mobile-menu" aria-expanded="false">
                <i class="fa-solid fa-bars"></i>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </header>

    <div id="mobile-menu" class="rx-mobile-menu">
        <a href="/" wire:navigate data-nav-link class="rx-nav-link {{ request()->is('/') ? 'is-active' : '' }}">خانه</a>
        <a href="/about" wire:navigate data-nav-link
            class="rx-nav-link {{ request()->is('about') ? 'is-active' : '' }}">درباره ما</a>
        <a href="/contact" wire:navigate data-nav-link
            class="rx-nav-link {{ request()->is('contact') ? 'is-active' : '' }}">تماس</a>

        <a href="/contact" wire:navigate class="rx-btn rx-btn-primary w-full sm:w-auto">
            <span>مشاوره رایگان</span>
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>
</div>