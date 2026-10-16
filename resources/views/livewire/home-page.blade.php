<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Support\Schema;

new #[Layout('components.layouts.app')]
    class extends Component {
    public $faqs = [];

    public function mount(): void
    {
        $this->faqs = Schema::faqForBlade();
    }

    public function rendering($view)
    {
        $view->layoutData([
            'title' => 'رندیکس | دیجیتال مارکتینگ در نجف آباد، طراحی وبسایت و سئو',
            'metaDescription' => 'رندیکس برای کسب‌وکارهای نجف آباد دیجیتال مارکتینگ، طراحی وبسایت اختصاصی، سئو، تبلیغات آنلاین، مدیریت شبکه‌های اجتماعی و فروشگاه اینترنتی را با هدف افزایش دیده‌شدن، اعتماد و فروش ارائه می‌دهد.',
            'keywords' => 'دیجیتال مارکتینگ نجف آباد, طراحی وبسایت نجف آباد, سئو در نجف آباد, تبلیغات آنلاین نجف آباد, مدیریت شبکه‌های اجتماعی نجف آباد, فروشگاه اینترنتی نجف آباد, آژانس دیجیتال مارکتینگ نجف آباد, بازاریابی اینترنتی نجف آباد, رشد فروش آنلاین نجف آباد, طراحی سایت اصفهان, بهینه‌سازی نرخ تبدیل, مشاوره دیجیتال مارکتینگ',
            'ogImage' => '/images/og-home.jpg',
            'schema' => [Schema::home()],
        ]);
    }
}; ?>

@php
    $keywords = [
        'دیجیتال مارکتینگ نجف آباد',
        'طراحی وبسایت اختصاصی',
        'سئو و رشد در گوگل',
        'تبلیغات آنلاین',
        'مدیریت شبکه‌های اجتماعی',
        'فروشگاه اینترنتی',
        'بهینه‌سازی نرخ تبدیل',
        'پشتیبانی مستمر',
    ];

    $services = [
        [
            'id' => 'digital-marketing',
            'icon' => 'fa-solid fa-bullhorn',
            'title' => 'دیجیتال مارکتینگ در نجف آباد',
            'text' => 'استراتژی رشد آنلاین، تبلیغات هدفمند، تولید محتوا و بهینه‌سازی قیف فروش برای کسب‌وکارهای محلی و ملی.',
            'metric' => 'رشد فروش',
            'metricNote' => 'از بازدید تا مشتری',
            'span' => 'md:col-span-7',
        ],
        [
            'id' => 'web-design',
            'icon' => 'fa-solid fa-code',
            'title' => 'طراحی وبسایت اختصاصی',
            'text' => 'وبسایت سریع، امن، سئوپدیده و متناسب با هویت برند؛ بدون قالب آماده و کد کثیف.',
            'metric' => '< 2s',
            'metricNote' => 'سرعت لود هدف',
            'span' => 'md:col-span-5',
        ],
        [
            'id' => 'seo',
            'icon' => 'fa-solid fa-magnifying-glass-chart',
            'title' => 'سئو و دیده‌شدن در گوگل',
            'text' => 'سئوی تکنیکال، محتوایی و محلی برای جذب مشتریانی که در نجف آباد و اصفهان جست‌وجو می‌کنند.',
            'metric' => '90+',
            'metricNote' => 'امتیاز فنی هدف',
            'span' => 'md:col-span-4',
        ],
        [
            'id' => 'social-media',
            'icon' => 'fa-solid fa-share-nodes',
            'title' => 'شبکه‌های اجتماعی و تبلیغات',
            'text' => 'مدیریت محتوا، کمپین‌های هدفمند و گزارش‌دهی عملکرد برای افزایش تعامل و لید واقعی.',
            'metric' => 'کمپین',
            'metricNote' => 'هدفمند و قابل اندازه‌گیری',
            'span' => 'md:col-span-4',
        ],
        [
            'id' => 'ecommerce',
            'icon' => 'fa-solid fa-bag-shopping',
            'title' => 'فروشگاه اینترنتی و CRO',
            'text' => 'ساخت فروشگاه آنلاین با تجربه خرید روان، پرداخت امن و بهینه‌سازی نرخ تبدیل.',
            'metric' => 'CRO',
            'metricNote' => 'بهبود مسیر خرید',
            'span' => 'md:col-span-4',
        ],
    ];

    $processSteps = [
        [
            'number' => '۰۱',
            'icon' => 'fa-solid fa-comments',
            'title' => 'گفتگوی کشف',
            'text' => 'اول می‌شنویم. هدف، مخاطب، رقبا و نقاط درد کسب‌وکار تو رو دقیق بررسی می‌کنیم.',
        ],
        [
            'number' => '۰۲',
            'icon' => 'fa-solid fa-map-location-dot',
            'title' => 'استراتژی و نقشه راه',
            'text' => 'بر اساس داده و تجربه، مسیر طراحی، سئو و مارکتینگ رو مشخص می‌کنیم.',
        ],
        [
            'number' => '۰۳',
            'icon' => 'fa-solid fa-code',
            'title' => 'طراحی و توسعه',
            'text' => 'رابط کاربری مدرن، کد تمیز، سرعت بالا و انیمیشن‌های معنادار ساخته می‌شه.',
        ],
        [
            'number' => '۰۴',
            'icon' => 'fa-solid fa-rocket',
            'title' => 'راه‌اندازی و رشد',
            'text' => 'سایت منتشر می‌شه، اما کار تمام نمی‌شه؛ بهینه‌سازی مستمر و پشتیبانی ادامه داره.',
        ],
    ];

    $whyItems = [
        [
            'icon' => 'fa-solid fa-eye',
            'title' => 'شفافیت کامل',
            'text' => 'بدون قول‌های الکی، بدون پنهان‌کاری. هزینه، زمان و مراحل کار رو شفاف می‌گیم.',
        ],
        [
            'icon' => 'fa-solid fa-gears',
            'title' => 'وسواس فنی',
            'text' => 'سرعت، امنیت، سئو و دسترسی‌پذیری برای ما اختیاری نیستن؛ بخشی از استانداردها.',
        ],
        [
            'icon' => 'fa-solid fa-heart',
            'title' => 'همراهی واقعی',
            'text' => 'ما مثل یک تیم داخلی کنار کسب‌وکار تو می‌مونیم، نه یک فروشنده‌ی پروژه.',
        ],
    ];
@endphp

<div class="relative">

    <section
        id="hero"
        class="rx-section rx-hero flex items-center section-tone-light"
        data-rx-scene
        data-scene-id="hero"
        data-scene-color="#45C8FF"
        data-scene-intensity="1.05"
        data-scene-speed="1"
    >
        <div class="rx-container relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                <div class="lg:col-span-7">
                    <span class="rx-badge rx-reveal">
                        <i class="fa-solid fa-location-crosshairs"></i>
                        نجف‌آباد · اصفهان · ایران
                    </span>

                    <h1 class="rx-display mt-6 rx-reveal">
                        سیستم
                        <span class="rx-gradient-text">رشد دیجیتال</span><br>
                        برای کسب‌وکارهای جدی
                    </h1>

                    <p class="rx-body-lg rx-muted mt-7 max-w-2xl rx-reveal">
                        دیجیتال مارکتینگ در نجف‌آباد یعنی ترکیب وبسایت سریع، سئوی درست، تبلیغات هوشمند و محتوای هدفمند؛
                        نه شلیک تصادفی به بازار. رندیکس مسیر دیده‌شدن، اعتماد و فروش را مهندسی می‌کند.
                    </p>

                    <div class="flex flex-wrap items-center gap-3 mt-9 rx-reveal">
                        <a href="/contact" wire:navigate class="rx-btn rx-btn-primary">
                            <span>مشاوره رایگان بگیر</span>
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>

                        <a href="#services" class="rx-btn rx-btn-outline">
                            <span>خدمات رندیکس</span>
                            <i class="fa-solid fa-arrow-down"></i>
                        </a>
                    </div>

                    <div class="rx-stack mt-8 rx-reveal">
                        @foreach ($keywords as $keyword)
                            <span class="rx-chip">
                                <i class="fa-solid fa-asterisk"></i>
                                {{ $keyword }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="lg:col-span-5 rx-reveal">
                    <div class="rx-panel p-6 md:p-7">
                        <div class="flex items-center justify-between gap-4 mb-6">
                            <span class="rx-eyebrow">LIVE SIGNAL</span>
                            <span class="rx-chip">
                                <i class="fa-solid fa-wave-square"></i>
                                growth mode
                            </span>
                        </div>

                        <div class="rx-pulse" aria-hidden="true">
                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-6">
                            <div class="rx-mini-stat">
                                <p class="rx-caption rx-soft">تمرکز بازار</p>
                                <p class="rx-h3 mt-1 rx-text-light">نجف‌آباد</p>
                            </div>

                            <div class="rx-mini-stat">
                                <p class="rx-caption rx-soft">پوشش خدمات</p>
                                <p class="rx-h3 mt-1 rx-text-primary">سراسر ایران</p>
                            </div>

                            <div class="rx-mini-stat">
                                <p class="rx-caption rx-soft">معماری فنی</p>
                                <p class="rx-h3 mt-1 rx-text-purple">Laravel + React</p>
                            </div>

                            <div class="rx-mini-stat">
                                <p class="rx-caption rx-soft">هدف نهایی</p>
                                <p class="rx-h3 mt-1 rx-text-light">فروش واقعی</p>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t" style="border-color: var(--rx-border);">
                            <p class="rx-body rx-muted">
                                یک وبسایت خوب، فقط ویترین نیست.  
                                <span class="rx-text-light font-bold">موتور رشد</span> کسب‌وکار توئه.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rx-hero-scroll rx-reveal" aria-hidden="true">
            <div class="rx-hero-scroll-line">
                <div class="rx-hero-scroll-dot"></div>
            </div>
        </div>
    </section>

    <section class="rx-proof-band py-8">
        <div class="rx-container">
            <dl class="rx-stats">
                <div class="rx-reveal">
                    <dt class="rx-caption rx-soft mb-2">پروژه موفق</dt>
                    <dd class="rx-h2">
                        <span class="rx-text-light" data-count="50" data-suffix="+">۰</span>
                    </dd>
                </div>

                <div class="rx-reveal">
                    <dt class="rx-caption rx-soft mb-2">رضایت مشتری</dt>
                    <dd class="rx-h2">
                        <span class="rx-text-primary" data-count="98" data-suffix="٪">۰</span>
                    </dd>
                </div>

                <div class="rx-reveal">
                    <dt class="rx-caption rx-soft mb-2">میانگین لود</dt>
                    <dd class="rx-h2">
                        <span class="rx-text-purple" data-count="2.3" data-decimals="1" data-suffix="s">۰</span>
                    </dd>
                </div>

                <div class="rx-reveal">
                    <dt class="rx-caption rx-soft mb-2">پشتیبانی</dt>
                    <dd class="rx-h2 rx-text-light">۲۴/۷</dd>
                </div>
            </dl>
        </div>
    </section>

    <section
        id="services"
        class="rx-section section-tone-primary"
        data-rx-scene
        data-scene-id="services"
        data-scene-color="#1769FF"
        data-scene-intensity="1.08"
        data-scene-speed="1.05"
    >
        <div class="rx-container relative z-10">
            <div class="max-w-3xl mx-auto text-center mb-16">
                <span class="rx-eyebrow rx-reveal">// SERVICES</span>

                <h2 class="rx-h1 mt-4 rx-reveal">
                    خدمات
                    <span class="rx-gradient-text">دیجیتال مارکتینگ و طراحی وب</span>
                    برای رشد کسب‌وکار
                </h2>

                <p class="rx-body-lg rx-muted mt-5 rx-reveal">
                    ما هر خدمت را جدا نمی‌فروشیم. هدف ما ساخت یک سیستم رشد است:  
                    مشتری درست، در زمان درست، با پیام درست، به سمت فروش هدایت شود.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 md:gap-5">
                @foreach ($services as $service)
                    <article id="{{ $service['id'] }}" class="rx-panel rx-bento-item {{ $service['span'] }} p-7 md:p-8 rx-reveal">
                        <div class="rx-card-content">
                            <div class="rx-icon-tile mb-5" style="width:3rem;height:3rem;border-radius:1rem;display:inline-flex;align-items:center;justify-content:center;color:var(--rx-section-accent);background:var(--rx-surface);border:1px solid var(--rx-border);font-size:1.15rem;">
                                <i class="{{ $service['icon'] }}"></i>
                            </div>

                            <h3 class="rx-h3">{{ $service['title'] }}</h3>

                            <p class="rx-body rx-muted mt-4">{{ $service['text'] }}</p>

                            <div class="mt-6 pt-5 border-t" style="border-color: var(--rx-border);">
                                <span class="rx-caption rx-soft">{{ $service['metricNote'] }}</span>
                                <p class="rx-h3 mt-1 rx-text-primary">{{ $service['metric'] }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach

                <article id="support" class="rx-panel md:col-span-12 p-7 md:p-8 rx-reveal">
                    <div class="rx-card-content grid grid-cols-1 lg:grid-cols-12 gap-7 items-center">
                        <div class="lg:col-span-8">
                            <h3 class="rx-h2">پشتیبانی یعنی تنها نذاشتن</h3>

                            <p class="rx-body rx-muted mt-4 max-w-3xl">
                                بعد از تحویل پروژه، رابطه ما تموم نمی‌شه.  
                                مانیتورینگ، به‌روزرسانی، امنیت، بهینه‌سازی سرعت و راهنمایی محتوایی بخشی از تعهد رندیکسه.
                            </p>
                        </div>

                        <div class="lg:col-span-4 flex lg:justify-end">
                            <a href="/contact" wire:navigate class="rx-btn rx-btn-outline w-full lg:w-auto">
                                <span>گفتگو با تیم رندیکس</span>
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section
        id="process"
        class="rx-section rx-section-alt section-tone-purple"
        data-rx-scene
        data-scene-id="process"
        data-scene-color="#7653F6"
        data-scene-intensity="1.12"
        data-scene-speed="0.95"
    >
        <div class="rx-container rx-method relative z-10">
            <aside class="rx-method-aside">
                <span class="rx-eyebrow rx-reveal">// PROCESS</span>

                <h2 class="rx-h1 mt-4 rx-reveal">
                    سفر تو با رندیکس
                    <span class="rx-gradient-text">چطور شروع می‌شه؟</span>
                </h2>

                <p class="rx-body-lg rx-muted mt-5 rx-reveal">
                    ما فرایند رو ساده، شفاف و بدون استرس کردیم.  
                    تو فقط ایده و هدف رو بگو؛ بقیه‌اش با ما.
                </p>

                <a href="/contact" wire:navigate class="rx-btn rx-btn-primary mt-7 rx-reveal">
                    <span>همین الان شروع کن</span>
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </aside>

            <div class="rx-method-list">
                @foreach ($processSteps as $step)
                    <div class="rx-method-step rx-reveal">
                        <div class="rx-method-num">
                            <i class="{{ $step['icon'] }}"></i>
                        </div>

                        <div class="rx-method-line"></div>

                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="rx-caption rx-soft font-mono">{{ $step['number'] }}</span>
                                <h3 class="rx-h3">{{ $step['title'] }}</h3>
                            </div>

                            <p class="rx-body rx-muted">{{ $step['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section
        class="rx-section section-tone-light"
        data-rx-scene
        data-scene-id="why"
        data-scene-color="#45C8FF"
        data-scene-intensity="1.05"
        data-scene-speed="1"
    >
        <div class="rx-container relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="rx-eyebrow rx-reveal">// WHY RENDIX</span>

                <h2 class="rx-h1 mt-4 rx-reveal">
                    چرا رندیکس
                    <span class="rx-gradient-text">متفاوته؟</span>
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($whyItems as $item)
                    <div class="rx-panel p-7 rx-reveal">
                        <div class="rx-card-content">
                            <div class="rx-icon-tile mb-5" style="width:3rem;height:3rem;border-radius:1rem;display:inline-flex;align-items:center;justify-content:center;color:var(--rx-section-accent);background:var(--rx-surface);border:1px solid var(--rx-border);font-size:1.15rem;">
                                <i class="{{ $item['icon'] }}"></i>
                            </div>

                            <h3 class="rx-h3">{{ $item['title'] }}</h3>
                            <p class="rx-body rx-muted mt-3">{{ $item['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section
        id="faq"
        class="rx-section rx-section-alt section-tone-purple"
        data-rx-scene
        data-scene-id="faq"
        data-scene-color="#7653F6"
        data-scene-intensity="1.18"
        data-scene-speed="0.9"
    >
        <div class="rx-container relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <div class="lg:col-span-4">
                    <span class="rx-eyebrow rx-reveal">// FAQ</span>

                    <h2 class="rx-h1 mt-4 rx-reveal">
                        سوالاتی که
                        <span class="rx-gradient-text">خیلی‌ها می‌پرسن</span>
                    </h2>

                    <p class="rx-body-lg rx-muted mt-5 rx-reveal">
                        اگر جواب سوالت اینجا نبود، مستقیم با ما صحبت کن.  
                        مشاوره اولیه رایگانه و بدون تعهد.
                    </p>

                    <a href="/contact" wire:navigate class="rx-btn rx-btn-outline mt-7 rx-reveal">
                        <span>پرسیدن سوال شخصی</span>
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>

                <div class="lg:col-span-8 space-y-3">
                    @foreach ($faqs as $faq)
                        @php $isOpen = $loop->first; @endphp

                        <div class="rx-faq-item rx-reveal" data-faq>
                            <button
                                type="button"
                                class="rx-faq-trigger"
                                data-faq-toggle
                                aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                            >
                                <span>{{ $faq['name'] }}</span>

                                <span class="rx-faq-icon {{ $isOpen ? 'is-open' : '' }}">
                                    <i class="fa-solid fa-plus"></i>
                                </span>
                            </button>

                            <div class="rx-faq-panel {{ $isOpen ? 'is-open' : '' }}" data-faq-panel>
                                <div class="rx-faq-panel-inner">
                                    <div class="rx-faq-answer">
                                        {{ $faq['text'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section
        class="rx-section section-tone-primary"
        data-rx-scene
        data-scene-id="cta"
        data-scene-color="#1769FF"
        data-scene-intensity="1.2"
        data-scene-speed="1.1"
    >
        <div class="rx-container relative z-10">
            <div class="rx-cta rx-reveal">
                <div class="rx-cta-inner p-8 md:p-12 text-center">
                    <span class="rx-badge">
                        <i class="fa-solid fa-rocket"></i>
                        زمان شروع
                    </span>

                    <h2 class="rx-display mt-6">
                        آماده‌ای
                        <span class="rx-gradient-text">دنیای دیجیتال</span><br>
                        کسب‌وکارت رو بسازی؟
                    </h2>

                    <p class="rx-body-lg rx-muted mt-7 max-w-2xl mx-auto">
                        اولین قدم فقط یک گفتگوی کوتاهه.  
                        بیا ببینیم رندیکس چطور می‌تونه برند تو رو دیده‌شدنی، باورکردنی و فروش‌محور کنه.
                    </p>

                    <div class="flex flex-wrap items-center justify-center gap-3 mt-9">
                        <a href="/contact" wire:navigate class="rx-btn rx-btn-primary">
                            <span>مشاوره رایگان</span>
                            <i class="fa-solid fa-rocket"></i>
                        </a>

                        <a href="tel:+989000000000" class="rx-btn rx-btn-outline">
                            <span>تماس تلفنی</span>
                            <i class="fa-solid fa-phone"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>