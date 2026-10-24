@php
    $siteName = setting('site.name', 'رندیکس');

    $logo = setting_url('brand.logo', '/images/favicon.png?v=6');

    $tagline = setting(
        'site.tagline',
        'ما فقط وبسایت نمی‌سازیم؛ مسیر رشد می‌سازیم. از نجف‌آباد، برای کسب‌وکارهایی که می‌خوان جدی دیده بشن.'
    );

    $contactEmail = setting('contact.email') ?: 'info@rendix.ir';

    $contactPhone = setting('contact.phone') ?: '۰۹۱۲ ۳۴۵ ۶۷۹';
    $contactPhoneLatin = \App\Support\Jalali::toLatinDigits((string) $contactPhone);
    $contactPhoneTel = preg_replace('/\D/', '', $contactPhoneLatin);

    $contactAddress = setting('contact.address') ?: 'نجف‌آباد، اصفهان';

    try {
        $footerServices = \App\Models\Service::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(4)
            ->pluck('title', 'slug')
            ->all();
    } catch (\Throwable $e) {
        $footerServices = [];
    }

    if (empty($footerServices)) {
        $footerServices = [
            'web-design' => 'طراحی وبسایت',
            'seo' => 'سئو',
            'digital-marketing' => 'دیجیتال مارکتینگ',
            'ecommerce' => 'فروشگاه اینترنتی',
        ];
    }

    $socials = [
        [
            'label' => 'اینستاگرام',
            'icon' => 'fa-brands fa-instagram',
            'url' => setting('social.instagram'),
        ],
        [
            'label' => 'تلگرام',
            'icon' => 'fa-brands fa-telegram',
            'url' => setting('social.telegram', 'https://t.me/RendixDMServices'),
        ],
        [
            'label' => 'لینکدین',
            'icon' => 'fa-brands fa-linkedin-in',
            'url' => setting('social.linkedin'),
        ],
        [
            'label' => 'واتساپ',
            'icon' => 'fa-brands fa-whatsapp',
            'url' => setting('social.whatsapp'),
        ],
    ];

    $preparedSocials = [];

    foreach ($socials as $social) {
        $url = trim((string) ($social['url'] ?? ''));

        if ($social['label'] === 'واتساپ' && $url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) {
            $url = 'https://wa.me/' . preg_replace('/\D/', '', \App\Support\Jalali::toLatinDigits($url));
        }

        if ($url !== '') {
            $preparedSocials[] = [
                'label' => $social['label'],
                'icon' => $social['icon'],
                'url' => $url,
            ];
        }
    }

    try {
        $jalaliYear = \App\Support\Jalali::toPersianDigits(
            \Morilog\Jalali\Jalalian::forge(now())->format('Y')
        );
    } catch (\Throwable $e) {
        $jalaliYear = '۱۴۴';
    }
@endphp

<footer class="relative mt-32 border-t border-white/5 bg-navy-2/40">
    <div class="max-w-7xl mx-auto px-5 md:px-10 py-16">

        {{-- بالا: برند + خبرنامه --}}
        <div
            class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8 pb-12 border-b border-white/5">
            <div class="space-y-3 max-w-md">
                <div class="flex items-center gap-3">
                    <img src="{{ $logo }}" alt="{{ $siteName }}" class="w-11 h-11">
                    <span class="text-2xl font-black">{{ $siteName }}</span>
                </div>

                <p class="text-sm text-snow/50 leading-7">
                    {{ $tagline }}
                </p>
            </div>

            <div class="w-full max-w-md">
                <p class="text-sm font-bold mb-3 text-snow/70">عضویت در خبرنامه رشد</p>

                <form class="flex gap-2" onsubmit="return false;">
                    <input type="email" placeholder="ایمیل شما"
                        class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm placeholder:text-snow/30 focus:outline-none focus:border-sky/50 transition-colors">
                    <button type="submit" class="btn btn-primary !px-5">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- لینک‌ها --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-10 py-12">
            <div class="space-y-4">
                <p class="font-bold text-sm">{{ $siteName }}</p>

                <ul class="space-y-3 text-sm text-snow/50">
                    <li>
                        <a href="{{ route('about') }}" wire:navigate class="hover:text-sky transition-colors">
                            درباره ما
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('request-project') }}" wire:navigate class="hover:text-sky transition-colors">
                            درخواست پروژه
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" wire:navigate class="hover:text-sky transition-colors">
                            تماس با ما
                        </a>
                    </li>

                    @if (Route::has('projects'))
                        <li>
                            <a href="{{ route('projects') }}" wire:navigate class="hover:text-sky transition-colors">
                                نمونه‌کارها
                            </a>
                        </li>
                    @endif

                    <li>
                        @auth
                            <a href="{{ route('dashboard') }}" wire:navigate class="hover:text-sky transition-colors">
                                داشبورد من
                            </a>
                        @else
                            <a href="{{ route('login') }}" wire:navigate class="hover:text-sky transition-colors">
                                ورود / ثبت‌نام
                            </a>
                        @endauth
                    </li>
                    
                    <li>
                        <a href="{{ url('/#faq') }}" class="hover:text-sky transition-colors">
                            سوالات متداول
                        </a>
                    </li>
                </ul>
            </div>

            <div class="space-y-4">
                <p class="font-bold text-sm">خدمات</p>

                <ul class="space-y-3 text-sm text-snow/50">
                    @foreach ($footerServices as $slug => $title)
                        <li>
                            <a href="{{ route('services.show', $slug) }}" class="hover:text-sky transition-colors">
                                {{ $title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="space-y-4">
                <p class="font-bold text-sm">تماس</p>

                <ul class="space-y-3 text-sm text-snow/50">
                    @if ($contactAddress)
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-sky/70"></i>
                            {{ $contactAddress }}
                        </li>
                    @endif

                    @if ($contactEmail)
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-sky/70"></i>
                            <a href="mailto:{{ $contactEmail }}" class="hover:text-sky transition-colors" dir="ltr">
                                {{ $contactEmail }}
                            </a>
                        </li>
                    @endif

                    @if ($contactPhone)
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-sky/70"></i>
                            <a href="tel:{{ $contactPhoneTel }}" class="hover:text-sky transition-colors">
                                {{ $contactPhone }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="space-y-4">
                <p class="font-bold text-sm">همراه ما باشید</p>

                <div class="flex gap-3">
                    @forelse ($preparedSocials as $social)
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                            title="{{ $social['label'] }}"
                            class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-snow/60 hover:text-sky hover:border-sky/40 transition-all">
                            <i class="{{ $social['icon'] }}"></i>
                        </a>
                    @empty
                        <a href="https://t.me/RendixDMServices" target="_blank" rel="noopener noreferrer" title="تلگرام"
                            class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-snow/60 hover:text-sky hover:border-sky/40 transition-all">
                            <i class="fa-brands fa-telegram"></i>
                        </a>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- پایین --}}
        <div
            class="flex flex-col md:flex-row items-center justify-between gap-4 pt-8 border-t border-white/5 text-xs text-snow/35">
            <p>
                © {{ $jalaliYear }} {{ $siteName }} — تمامی حقوق محفوظ است.
            </p>

            <p class="flex items-center gap-1.5">
                ساخته‌شده با <i class="fa-solid fa-heart text-primary"></i> در نجف‌آباد
            </p>
        </div>
    </div>
</footer>