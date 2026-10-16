<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

@php
    $serviceLinks = [
        ['title' => 'دیجیتال مارکتینگ', 'href' => '/#digital-marketing', 'icon' => 'fa-solid fa-bullhorn'],
        ['title' => 'طراحی وبسایت', 'href' => '/#web-design', 'icon' => 'fa-solid fa-code'],
        ['title' => 'سئو و گوگل', 'href' => '/#seo', 'icon' => 'fa-solid fa-magnifying-glass-chart'],
        ['title' => 'شبکه‌های اجتماعی', 'href' => '/#social-media', 'icon' => 'fa-solid fa-share-nodes'],
        ['title' => 'فروشگاه اینترنتی', 'href' => '/#ecommerce', 'icon' => 'fa-solid fa-bag-shopping'],
        ['title' => 'پشتیبانی سایت', 'href' => '/#support', 'icon' => 'fa-solid fa-headset'],
    ];

    $companyLinks = [
        ['title' => 'فرآیند همکاری', 'href' => '/#process', 'icon' => 'fa-solid fa-route'],
        ['title' => 'سوالات متداول', 'href' => '/#faq', 'icon' => 'fa-solid fa-circle-question'],
        ['title' => 'درباره رندیکس', 'href' => '/about', 'icon' => 'fa-solid fa-user-group', 'navigate' => true],
        ['title' => 'مشاوره رایگان', 'href' => '/contact', 'icon' => 'fa-solid fa-paper-plane', 'navigate' => true],
    ];

    $coverageAreas = ['نجف‌آباد', 'اصفهان', 'شهرضا', 'خمینی‌شهر', 'لنجان', 'سراسر ایران'];

    $trustItems = [
        ['icon' => 'fa-solid fa-file-contract', 'title' => 'قرارداد شفاف'],
        ['icon' => 'fa-solid fa-bolt', 'title' => 'تحویل به‌موقع'],
        ['icon' => 'fa-solid fa-shield-halved', 'title' => 'پشتیبانی مستمر'],
    ];
@endphp

<footer class="rx-footer">
    <div class="rx-footer-top-line"></div>

    <div class="rx-container py-14 md:py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-12">
            <div class="sm:col-span-2 lg:col-span-4">
                <a href="/" wire:navigate class="rx-logo-link mb-6" aria-label="صفحه اصلی رندیکس">
                    <img src="/images/logo.png?v=5" alt="لوگوی رندیکس" width="180" height="70" loading="lazy"
                        decoding="async" class="rx-logo-img h-14 md:h-16 w-auto">
                    <span class="sr-only">رندیکس</span>
                </a>

                <p class="rx-body rx-muted max-w-md">
                    رندیکس یک استودیوی فنی-خلاق برای طراحی وبسایت، سئو، دیجیتال مارکتینگ و رشد آنلاین است.
                    ما با توجه به سرعت، کیفیت فنی، تجربه کاربری و نتیجه تجاری، سیستم دیجیتال کسب‌وکار را می‌سازیم.
                </p>

                <div class="rx-stack mt-6">
                    @foreach ($trustItems as $trust)
                        <span class="rx-chip">
                            <i class="{{ $trust['icon'] }}"></i>
                            {{ $trust['title'] }}
                        </span>
                    @endforeach
                </div>

                <div class="flex items-center gap-2 mt-7">
                    <a href="#" class="rx-icon-btn" aria-label="اینستاگرام رندیکس">
                        <i class="fa-brands fa-instagram"></i>
                    </a>

                    <a href="#" class="rx-icon-btn" aria-label="لینکدین رندیکس">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>

                    <a href="https://t.me/RendixDMServices" target="_blank" rel="noopener noreferrer"
                        class="rx-icon-btn" aria-label="کانال تلگرام رندیکس">
                        <i class="fa-brands fa-telegram"></i>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-2">
                <h4 class="rx-h3 mb-5">خدمات</h4>

                <ul class="space-y-3">
                    @foreach ($serviceLinks as $link)
                        <li>
                            <a href="{{ $link['href'] }}"
                                class="rx-body rx-muted hover:rx-text-light transition-colors inline-flex items-center gap-2">
                                <i class="{{ $link['icon'] }} rx-text-primary"></i>
                                {{ $link['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h4 class="rx-h3 mb-5">مسیر همکاری</h4>

                <ul class="space-y-3">
                    @foreach ($companyLinks as $link)
                        <li>
                            <a href="{{ $link['href'] }}" @if(!empty($link['navigate'])) wire:navigate @endif
                                class="rx-body rx-muted hover:rx-text-light transition-colors inline-flex items-center gap-2">
                                <i class="{{ $link['icon'] }} rx-text-purple"></i>
                                {{ $link['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="sm:col-span-2 lg:col-span-4">
                <div class="rx-panel p-6 h-full">
                    <div class="rx-card-content">
                        <h4 class="rx-h3 mb-5">اطلاعات تماس</h4>

                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-phone mt-1 rx-text-primary"></i>
                                <div>
                                    <p class="rx-caption rx-soft">تلفن</p>
                                    <a href="tel:+989000000000"
                                        class="rx-body font-bold hover:rx-text-light transition-colors rx-breakable">
                                        ۰۹۰-۰۰۰-۰۰۰۰
                                    </a>
                                </div>
                            </li>

                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-envelope mt-1 rx-text-purple"></i>
                                <div>
                                    <p class="rx-caption rx-soft">ایمیل</p>
                                    <a href="mailto:hello@rendix.ir"
                                        class="rx-body font-bold hover:rx-text-light transition-colors rx-breakable">
                                        hello@rendix.ir
                                    </a>
                                </div>
                            </li>

                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-location-dot mt-1 rx-text-light"></i>
                                <div>
                                    <p class="rx-caption rx-soft">موقعیت</p>
                                    <p class="rx-body font-bold">نجف‌آباد، اصفهان، ایران</p>
                                </div>
                            </li>

                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-clock mt-1 rx-text-primary"></i>
                                <div>
                                    <p class="rx-caption rx-soft">ساعات کاری</p>
                                    <p class="rx-body font-bold">شنبه تا چهارشنبه | ۹ صبح تا ۶ عصر</p>
                                </div>
                            </li>
                        </ul>

                        <div class="mt-6 pt-5 border-t" style="border-color: var(--rx-border);">
                            <p class="rx-caption rx-soft mb-3">مناطق تحت پوشش</p>

                            <div class="rx-stack">
                                @foreach ($coverageAreas as $area)
                                    <span class="rx-chip">
                                        <i class="fa-solid fa-location-dot"></i>
                                        {{ $area }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-6">
                            <a href="/contact" wire:navigate class="rx-btn rx-btn-primary w-full">
                                <span>مشاوره رایگان</span>
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>

                            <a href="https://maps.google.com/?q=%D9%86%D8%AC%D9%81%E2%80%8C%D8%A2%D8%A8%D8%A7%D8%8C%20%D8%A7%D8%B5%D9%81%D9%87%D8%A7%D9%86"
                                target="_blank" rel="noopener noreferrer" class="rx-btn rx-btn-outline w-full">
                                <span>مشاهده موقعیت</span>
                                <i class="fa-solid fa-map-location-dot"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 pt-7 border-t flex flex-col md:flex-row items-center justify-between gap-4"
            style="border-color: var(--rx-border);">
            <p class="rx-caption rx-soft text-center md:text-right">
                © ۱۴ رندیکس. تمام حقوق محفوظ است.
            </p>

            <p class="rx-caption rx-soft font-mono text-center">
                طراحی و توسعه با <i class="fa-solid fa-heart rx-text-purple"></i> توسط رندیکس
            </p>

            <p class="rx-caption rx-soft text-center md:text-left">
                نسخه وب: <span class="rx-text-light font-mono">Rendix 3.0</span>
            </p>
        </div>
    </div>
</footer>