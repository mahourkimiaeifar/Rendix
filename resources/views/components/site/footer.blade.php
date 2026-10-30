@php
    $siteName = setting('site.name', 'رندیکس');
    $logo = setting_url('brand.logo', asset('images/favicon.png?v=6'));
    $tagline = setting('site.tagline', 'طراحی وبسایت، سئو و دیجیتال مارکتینگ');

    $contactEmail = setting('contact.email');
    $contactPhone = setting('contact.phone');
    $contactAddress = setting('contact.address');
    $contactPhoneTel = preg_replace('/\D/', '', (string) $contactPhone);

    $socials = [
        ['label' => 'تلگرام', 'icon' => 'fa-brands fa-telegram', 'url' => setting('social.telegram')],
        ['label' => 'اینستاگرام', 'icon' => 'fa-brands fa-instagram', 'url' => setting('social.instagram')],
        ['label' => 'واتساپ', 'icon' => 'fa-brands fa-whatsapp', 'url' => setting('social.whatsapp')],
        ['label' => 'لینکدین', 'icon' => 'fa-brands fa-linkedin-in', 'url' => setting('social.linkedin')],
    ];

    $preparedSocials = [];

    foreach ($socials as $social) {
        $url = trim((string) ($social['url'] ?? ''));

        if ($url !== '') {
            $preparedSocials[] = [
                'label' => $social['label'],
                'icon' => $social['icon'],
                'url' => $url,
            ];
        }
    }

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

    try {
        $legalPages = \App\Models\Page::query()
            ->active()
            ->ordered()
            ->limit(4)
            ->pluck('title', 'slug')
            ->all();
    } catch (\Throwable $e) {
        $legalPages = [];
    }

    $quickLinks = [];

    if (Route::has('about')) {
        $quickLinks[] = ['label' => 'درباره ما', 'url' => route('about'), 'icon' => 'fa-solid fa-circle-info'];
    }

    if (Route::has('services')) {
        $quickLinks[] = ['label' => 'خدمات', 'url' => route('services'), 'icon' => 'fa-solid fa-layer-group'];
    }

    if (Route::has('projects')) {
        $quickLinks[] = ['label' => 'نمونه‌کارها', 'url' => route('projects'), 'icon' => 'fa-solid fa-diagram-project'];
    }

    if (Route::has('blog')) {
        $quickLinks[] = ['label' => 'بلاگ', 'url' => route('blog'), 'icon' => 'fa-solid fa-newspaper'];
    }

    if (Route::has('request-project')) {
        $quickLinks[] = ['label' => 'درخواست پروژه', 'url' => route('request-project'), 'icon' => 'fa-solid fa-clipboard-list'];
    }

    if (Route::has('contact')) {
        $quickLinks[] = ['label' => 'تماس با ما', 'url' => route('contact'), 'icon' => 'fa-solid fa-paper-plane'];
    }

    try {
        $jalaliDate = \App\Support\Jalali::date(now());
        $year = \App\Support\Jalali::toPersianDigits(explode('/', $jalaliDate)[0] ?? now()->format('Y'));
    } catch (\Throwable $e) {
        $year = now()->format('Y');
    }
@endphp

<footer class="rxds-footer">
    <div class="rxds-footer-inner">
        <div class="rxds-footer-top">
            <div class="rxds-footer-brand">
                <div class="rxds-footer-logo">
                    <img src="{{ $logo }}" alt="{{ $siteName }}">
                    <span>{{ $siteName }}</span>
                </div>

                <p class="rxds-footer-desc">
                    {{ $tagline }}
                </p>

                @if (!empty($preparedSocials))
                    <div class="rxds-footer-social">
                        @foreach ($preparedSocials as $social)
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="rxds-social-btn"
                                title="{{ $social['label'] }}" aria-label="{{ $social['label'] }}">
                                <i class="{{ $social['icon'] }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rxds-footer-cta">
                <h3>آماده‌ای رشد دیجیتال را شروع کنی؟</h3>
                <p>
                    پروژه‌ات را مرحله‌به‌مرحله تعریف کن؛ ما بر اساس هدف، بودجه و زمان‌بندی، مسیر اجرا را پیشنهاد
                    می‌دهیم.
                </p>

                <div class="flex flex-wrap gap-3">
                    @if (Route::has('request-project'))
                        <a href="{{ route('request-project') }}" class="rxds-btn rxds-btn-primary rxds-btn-sm"
                            wire:navigate>
                            <i class="fa-solid fa-rocket"></i>
                            <span>درخواست پروژه</span>
                        </a>
                    @endif

                    @if (Route::has('contact'))
                        <a href="{{ route('contact') }}" class="rxds-btn rxds-btn-ghost rxds-btn-sm" wire:navigate>
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>تماس</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="rxds-footer-grid">
            <div class="rxds-footer-col">
                <h4>دسترسی سریع</h4>

                <ul class="rxds-footer-links">
                    @foreach ($quickLinks as $link)
                        <li>
                            <a href="{{ $link['url'] }}" wire:navigate>
                                <i class="{{ $link['icon'] }}"></i>
                                <span>{{ $link['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="rxds-footer-col">
                <h4>خدمات</h4>

                <ul class="rxds-footer-links">
                    @forelse ($footerServices as $slug => $title)
                        <li>
                            @if (Route::has('services.show'))
                                <a href="{{ route('services.show', $slug) }}" wire:navigate>
                                    <i class="fa-solid fa-arrow-left"></i>
                                    <span>{{ $title }}</span>
                                </a>
                            @elseif (Route::has('contact'))
                                <a href="{{ route('contact', ['service' => $slug]) }}" wire:navigate>
                                    <i class="fa-solid fa-arrow-left"></i>
                                    <span>{{ $title }}</span>
                                </a>
                            @endif
                        </li>
                    @empty
                        <li>
                            <a href="{{ Route::has('services') ? route('services') : '#' }}" wire:navigate>
                                <i class="fa-solid fa-layer-group"></i>
                                <span>همه خدمات</span>
                            </a>
                        </li>
                    @endforelse
                </ul>
            </div>

            <div class="rxds-footer-col">
                <h4>صفحات قانونی</h4>

                <ul class="rxds-footer-links">
                    @forelse ($legalPages as $slug => $title)
                        @if (Route::has('page'))
                            <li>
                                <a href="{{ route('page', $slug) }}" wire:navigate>
                                    <i class="fa-solid fa-file-lines"></i>
                                    <span>{{ $title }}</span>
                                </a>
                            </li>
                        @endif
                    @empty
                        <li>
                            <span class="rxds-footer-links text-xs">
                                صفحات قانونی از پنل مدیریت قابل اضافه‌کردن است.
                            </span>
                        </li>
                    @endforelse
                </ul>
            </div>

            <div class="rxds-footer-col">
                <h4>تماس با ما</h4>

                <div class="rxds-footer-contact">
                    @if ($contactAddress)
                        <div class="rxds-footer-contact-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ $contactAddress }}</span>
                        </div>
                    @endif

                    @if ($contactEmail)
                        <div class="rxds-footer-contact-item">
                            <i class="fa-solid fa-envelope"></i>
                            <a href="mailto:{{ $contactEmail }}" class="hover:text-sky transition-colors" dir="ltr">
                                {{ $contactEmail }}
                            </a>
                        </div>
                    @endif

                    @if ($contactPhone)
                        <div class="rxds-footer-contact-item">
                            <i class="fa-solid fa-phone"></i>
                            <a href="tel:{{ $contactPhoneTel }}" class="hover:text-sky transition-colors">
                                {{ $contactPhone }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="rxds-footer-bottom">
            <p>
                © {{ $year }} {{ $siteName }} — تمامی حقوق محفوظ است.
            </p>

            <p class="flex items-center gap-2">
                <i class="fa-solid fa-heart text-rose-400"></i>
                ساخته‌شده برای رشد کسب‌وکارهای جدی
            </p>
        </div>
    </div>
</footer>