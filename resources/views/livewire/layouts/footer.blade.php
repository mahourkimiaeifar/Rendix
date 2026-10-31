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

    $legalPages = [];

    if (class_exists(\App\Models\Page::class)) {
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

    $year = now()->format('Y');

    if (class_exists(\App\Support\Jalali::class)) {
        try {
            $jalaliDate = \App\Support\Jalali::date(now());
            $year = \App\Support\Jalali::toPersianDigits(explode('/', $jalaliDate)[0] ?? $year);
        } catch (\Throwable $e) {
            //
        }
    }
@endphp

<footer class="rx-footer">
    <div class="rx-footer-inner">
        <div class="rx-footer-top">
            <div class="rx-footer-brand">
                <a href="{{ Route::has('home') ? route('home') : url('/') }}" class="rx-footer-logo" wire:navigate>
                    <img src="{{ $logo }}" alt="{{ $siteName }}">
                    <span>{{ $siteName }}</span>
                </a>

                <p class="rx-footer-desc">
                    {{ $tagline }}
                </p>

                @if (!empty($preparedSocials))
                    <div class="rx-footer-social">
                        @foreach ($preparedSocials as $social)
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="rx-social-link"
                                title="{{ $social['label'] }}" aria-label="{{ $social['label'] }}">
                                <i class="{{ $social['icon'] }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rx-footer-cta">
                <h3>آماده‌ای رشد دیجیتال را شروع کنی؟</h3>

                <p>
                    پروژه‌ات را مرحله‌به‌مرحله تعریف کن؛ ما بر اساس هدف، بودجه و زمان‌بندی، مسیر اجرا را پیشنهاد
                    می‌دهیم.
                </p>

                <div class="flex flex-wrap gap-3">
                    @if (Route::has('request-project'))
                        <a href="{{ route('request-project') }}" class="btn btn-primary btn-sm" wire:navigate>
                            <i class="fa-solid fa-rocket"></i>
                            <span>درخواست پروژه</span>
                        </a>
                    @endif

                    @if (Route::has('contact'))
                        <a href="{{ route('contact') }}" class="btn btn-ghost btn-sm" wire:navigate>
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>تماس</span>
                        </a>
                    @endif
                </div>

                <form method="POST" action="{{ route('newsletter.subscribe') }}" class="rx-news-form">
                    @csrf

                    <input type="email" name="email" required dir="ltr" placeholder="your@email.com — خبرنامه رشد"
                        class="rx-news-input">

                    <button type="submit" class="btn btn-ghost btn-sm" title="عضویت در خبرنامه">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>

                @if (session('newsletter_status') === 'subscribed')
                    <p class="rx-news-flash ok">عضویتت در خبرنامه رشد ثبت شد 🎉</p>
                @elseif (session('newsletter_status') === 'unsubscribed')
                    <p class="rx-news-flash off">از خبرنامه خارج شدی؛ هر وقت خواستی برگرد.</p>
                @endif
            </div>
        </div>

        <div class="rx-footer-grid">
            <div class="rx-footer-col">
                <h4>دسترسی سریع</h4>

                <ul class="rx-footer-links">
                    @foreach ($quickLinks as $link)
                        <li>
                            <a href="{{ $link['url'] }}" class="rx-footer-link" wire:navigate>
                                <i class="{{ $link['icon'] }}"></i>
                                <span>{{ $link['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="rx-footer-col">
                <h4>خدمات</h4>

                <ul class="rx-footer-links">
                    @forelse ($footerServices as $slug => $title)
                        <li>
                            @if (Route::has('services.show'))
                                <a href="{{ route('services.show', $slug) }}" class="rx-footer-link" wire:navigate>
                                    <i class="fa-solid fa-arrow-left"></i>
                                    <span>{{ $title }}</span>
                                </a>
                            @elseif (Route::has('contact'))
                                <a href="{{ route('contact', ['service' => $slug]) }}" class="rx-footer-link" wire:navigate>
                                    <i class="fa-solid fa-arrow-left"></i>
                                    <span>{{ $title }}</span>
                                </a>
                            @endif
                        </li>
                    @empty
                        <li>
                            @if (Route::has('services'))
                                <a href="{{ route('services') }}" class="rx-footer-link" wire:navigate>
                                    <i class="fa-solid fa-layer-group"></i>
                                    <span>همه خدمات</span>
                                </a>
                            @endif
                        </li>
                    @endforelse
                </ul>
            </div>

            <div class="rx-footer-col">
                <h4>صفحات قانونی</h4>

                <ul class="rx-footer-links">
                    @forelse ($legalPages as $slug => $title)
                        @if (Route::has('page'))
                            <li>
                                <a href="{{ route('page', $slug) }}" class="rx-footer-link" wire:navigate>
                                    <i class="fa-solid fa-file-lines"></i>
                                    <span>{{ $title }}</span>
                                </a>
                            </li>
                        @endif
                    @empty
                        <li>
                            <span class="rx-footer-link" style="cursor: default;">
                                <i class="fa-solid fa-scale-balanced"></i>
                                <span>از پنل قابل افزودن است</span>
                            </span>
                        </li>
                    @endforelse
                </ul>
            </div>

            <div class="rx-footer-col">
                <h4>تماس با ما</h4>

                <div class="rx-footer-contact">
                    @if ($contactAddress)
                        <div class="rx-footer-contact-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ $contactAddress }}</span>
                        </div>
                    @endif

                    @if ($contactEmail)
                        <div class="rx-footer-contact-item">
                            <i class="fa-solid fa-envelope"></i>
                            <a href="mailto:{{ $contactEmail }}" dir="ltr">{{ $contactEmail }}</a>
                        </div>
                    @endif

                    @if ($contactPhone)
                        <div class="rx-footer-contact-item">
                            <i class="fa-solid fa-phone"></i>
                            <a href="tel:{{ $contactPhoneTel }}">{{ $contactPhone }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="rx-footer-bottom">
            <p>© {{ $year }} {{ $siteName }} — تمامی حقوق محفوظ است.</p>

            <p class="flex items-center gap-2">
                <i class="fa-solid fa-heart text-rose-400"></i>
                ساخته‌شده برای رشد کسب‌وکارهای جدی
            </p>
        </div>
    </div>
    @include('components.rx-shell')
</footer>

@once
    <script>
        (function () {
            function initReveal() {
                document.documentElement.classList.add('rx-reveal-ready');

                var els = document.querySelectorAll('.rv:not(.rv-in)');

                if (!('IntersectionObserver' in window)) {
                    els.forEach(function (el) { el.classList.add('rv-in'); });
                    return;
                }

                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('rv-in');
                            io.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

                els.forEach(function (el) { io.observe(el); });
            }

            function initCounters() {
                var nums = document.querySelectorAll('[data-count]');

                if (!('IntersectionObserver' in window)) {
                    nums.forEach(function (el) {
                        el.textContent = Number(el.dataset.count).toLocaleString('fa-IR') + (el.dataset.suffix || '');
                    });
                    return;
                }

                var io2 = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting) { return; }

                        var el = entry.target;
                        io2.unobserve(el);

                        var target = parseFloat(el.dataset.count) || 0;
                        var suffix = el.dataset.suffix || '';
                        var duration = 1300;
                        var start = null;

                        function step(ts) {
                            if (!start) { start = ts; }
                            var p = Math.min((ts - start) / duration, 1);
                            var eased = 1 - Math.pow(1 - p, 3);
                            var val = Math.floor(eased * target);
                            el.textContent = val.toLocaleString('fa-IR') + suffix;
                            if (p < 1) { requestAnimationFrame(step); }
                        }

                        requestAnimationFrame(step);
                    });
                }, { threshold: 0.4 });

                nums.forEach(function (el) { io2.observe(el); });
            }

            document.addEventListener('DOMContentLoaded', function () { initReveal(); initCounters(); });
            document.addEventListener('livewire:navigated', function () { initReveal(); initCounters(); });
        })();
    </script>
@endonce