<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Support\Schema;

new #[Layout('components.layouts.app')]
    class extends Component {
    public function rendering($view)
    {
        $view->layoutData([
            'title' => 'درباره رندیکس | نجف آباد',
            'metaDescription' => 'آشنایی با رندیکس؛ استودیوی فنی-خلاق با تمرکز اولیه بر بازار نجف آباد و ارائه خدمات طراحی وبسایت، سئو و دیجیتال مارکتینگ به کسب‌وکارهای سراسر ایران.',
            'keywords' => 'نجف آباد',
            'ogImage' => '/images/og-about.jpg',
            'schema' => [Schema::about()],
        ]);
    }
}; ?>

@php
    $values = [
        [
            'icon' => 'fa-solid fa-handshake',
            'title' => 'صداقت',
            'text' => 'اگر چیزی شدنی نباشه، صادقانه می‌گیم. اگر راه بهتر باشه، پیشنهاد می‌دیم.',
        ],
        [
            'icon' => 'fa-solid fa-ruler-combined',
            'title' => 'دقت',
            'text' => 'جزئیات برای ما مهمه؛ از فاصله‌ی متن‌ها تا سرعت لود و ساختار سئو.',
        ],
        [
            'icon' => 'fa-solid fa-gauge-high',
            'title' => 'سرعت',
            'text' => 'زمان تحویل و نتیجه‌گیری برامون اولویت داره. پروژه رو بی‌دلیل کش نمی‌دیم.',
        ],
        [
            'icon' => 'fa-solid fa-people-group',
            'title' => 'همراهی',
            'text' => 'تحویل پروژه پایان کار نیست؛ شروع یک رابطه‌ی بلندمدته.',
        ],
    ];

    $stack = [
        [
            'title' => 'Engineering',
            'icon' => 'fa-solid fa-server',
            'items' => ['Laravel', 'Livewire', 'React', 'Three.js', 'Vite', 'Tailwind', 'SEO technical'],
        ],
        [
            'title' => 'Product Design',
            'icon' => 'fa-solid fa-pen-nib',
            'items' => ['UI/UX', 'Motion design', 'Accessibility', 'Responsive systems'],
        ],
        [
            'title' => 'Growth',
            'icon' => 'fa-solid fa-bullhorn',
            'items' => ['Keyword research', 'Content strategy', 'Digital campaigns', 'Analytics'],
        ],
    ];
@endphp

<div class="relative">

    <section class="rx-section rx-hero rx-hero-sm flex items-center section-tone-light" data-rx-scene
        data-scene-id="about-hero" data-scene-color="#45C8FF" data-scene-intensity="1.05" data-scene-speed="1">
        <div class="rx-container relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-8">
                    <span class="rx-eyebrow rx-reveal">// ABOUT RENDIX</span>

                    <h1 class="rx-display mt-6 rx-reveal">
                        رندیکس؛
                        <span class="rx-gradient-text">یک استودیوی فنی-خلاق</span><br>
                        با ریشه در نجف‌آباد
                    </h1>

                    <p class="rx-body-lg rx-muted mt-8 max-w-3xl rx-reveal">
                        ما باور داریم کسب‌وکارهای ایرانی لایق وبسایت‌هایی هستن که هم دنیا ببینه، هم مشتری واقعی بیارن.
                        تمرکز اولیه‌مان روی بازار نجف‌آباد و اصفهان است، اما کیفیت، سرعت و استانداردهایمان را فراتر از
                        یک شهر تعریف می‌کنیم.
                    </p>

                    <div class="flex flex-wrap gap-4 mt-10 rx-reveal">
                        <a href="/contact" wire:navigate class="rx-btn rx-btn-primary">
                            <span>گفتگو با ما</span>
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>

                        <a href="/#services" class="rx-btn rx-btn-outline">
                            <span>خدمات رندیکس</span>
                            <i class="fa-solid fa-arrow-down"></i>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4 rx-reveal">
                    <div class="rx-panel p-6">
                        <h2 class="rx-h3 mb-5">شناسنامه کوتاه</h2>

                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-location-dot mt-1 rx-text-light"></i>
                                <div>
                                    <p class="rx-caption rx-soft">مبدأ</p>
                                    <p class="rx-body font-bold">نجف‌آباد، اصفهان</p>
                                </div>
                            </li>

                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-globe mt-1 rx-text-primary"></i>
                                <div>
                                    <p class="rx-caption rx-soft">پوشش</p>
                                    <p class="rx-body font-bold">ایران و بازارهای آنلاین</p>
                                </div>
                            </li>

                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-layer-group mt-1 rx-text-purple"></i>
                                <div>
                                    <p class="rx-caption rx-soft">تخصص</p>
                                    <p class="rx-body font-bold">وب، سئو، دیجیتال مارکتینگ</p>
                                </div>
                            </li>

                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-bolt mt-1 rx-text-light"></i>
                                <div>
                                    <p class="rx-caption rx-soft">رویکرد</p>
                                    <p class="rx-body font-bold">سرعت، کیفیت، نتیجه</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rx-section rx-section-alt section-tone-primary" data-rx-scene data-scene-id="about-story"
        data-scene-color="#1769FF" data-scene-intensity="1.08" data-scene-speed="0.95">
        <div class="rx-container relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <div class="lg:col-span-4">
                    <span class="rx-eyebrow rx-reveal">// MANIFESTO</span>

                    <h2 class="rx-h1 mt-4 rx-reveal">
                        از یک ایده تا یک
                        <span class="rx-gradient-text">دنیای دیجیتال</span>
                    </h2>
                </div>

                <div class="lg:col-span-8 space-y-6">
                    <p class="rx-body-lg rx-muted rx-reveal">
                        خیلی وقت‌ها صاحب کسب‌وکار می‌گه: «فقط یه سایت می‌خوام.»
                        اما سوال واقعی این‌جاست: «این سایت قراره چه تغییری در فروش، اعتماد و دیده‌شدن برند ایجاد کنه؟»
                    </p>

                    <blockquote class="rx-quote rx-reveal">
                        ما کد نمی‌نویسیم فقط؛ مسیر تبدیل می‌سازیم.
                        هر دکمه، هر سرعت، هر عنوان و هر ساختار باید یک هدف تجاری داشته باشه.
                    </blockquote>

                    <p class="rx-body rx-muted rx-reveal">
                        رندیکس دقیقاً برای همین سوال ساخته شد. ما قبل از نوشتن یک خط کد، هدف تجاری، مخاطب، رقبا و مسیر
                        تبدیل رو بررسی می‌کنیم.
                        بعد طراحی می‌کنیم، می‌سازیم، بهینه می‌کنیم و همراه مشتری می‌مونیم.
                    </p>

                    <p class="rx-body rx-muted rx-reveal">
                        نجف‌آباد نقطه شروع و تمرکز اولیه ماست؛ اما تعریف ما از کیفیت، محلی نیست.
                        ما می‌خوایم خروجی کارمان طوری باشه که در سطح ملی و حتی بین‌المللی قابل دفاع باشه.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="rx-section section-tone-purple" data-rx-scene data-scene-id="about-mission"
        data-scene-color="#7653F6" data-scene-intensity="1.08" data-scene-speed="1">
        <div class="rx-container relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="rx-panel p-8 rx-reveal">
                    <div class="rx-card-content">
                        <div class="rx-icon-tile mb-5"
                            style="width:3rem;height:3rem;border-radius:1rem;display:inline-flex;align-items:center;justify-content:center;color:var(--rx-section-accent);background:var(--rx-surface);border:1px solid var(--rx-border);font-size:1.15rem;">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>

                        <h2 class="rx-h2">ماموریت ما</h2>

                        <p class="rx-body rx-muted mt-4">
                            ساخت حضور دیجیتال قابل اعتماد برای کسب‌وکارها؛ به‌گونه‌ای که وبسایت فقط یک ویترین نباشه،
                            بلکه ابزاری برای جذب مشتری، افزایش فروش و رشد بلندمدت باشه.
                        </p>
                    </div>
                </div>

                <div class="rx-panel p-8 rx-reveal">
                    <div class="rx-card-content">
                        <div class="rx-icon-tile mb-5"
                            style="width:3rem;height:3rem;border-radius:1rem;display:inline-flex;align-items:center;justify-content:center;color:var(--rx-section-accent);background:var(--rx-surface);border:1px solid var(--rx-border);font-size:1.15rem;">
                            <i class="fa-solid fa-eye"></i>
                        </div>

                        <h2 class="rx-h2">چشم‌انداز ما</h2>

                        <p class="rx-body rx-muted mt-4">
                            تبدیل شدن به یکی از استودیوهای معتبر دیجیتال مارکتینگ و طراحی وب در ایران؛
                            با تمرکز بر کیفیت فنی، تجربه کاربری مدرن و نتایج قابل اندازه‌گیری برای مشتری.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rx-section rx-section-alt section-tone-purple" data-rx-scene data-scene-id="about-values"
        data-scene-color="#7653F6" data-scene-intensity="1.12" data-scene-speed="1">
        <div class="rx-container relative z-10">
            <div class="max-w-3xl mx-auto mb-14">
                <span class="rx-eyebrow rx-reveal">// VALUES</span>

                <h2 class="rx-h1 mt-4 rx-reveal">
                    ارزش‌هایی که
                    <span class="rx-gradient-text">کارمون رو تعریف می‌کنن</span>
                </h2>
            </div>

            <div class="rx-panel p-6 md:p-8 rx-reveal">
                @foreach ($values as $index => $value)
                    <div class="rx-value-row">
                        <div class="rx-value-index">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>

                        <div>
                            <h3 class="rx-h3 flex items-center gap-2">
                                <i class="{{ $value['icon'] }} rx-text-purple"></i>
                                {{ $value['title'] }}
                            </h3>

                            <p class="rx-body rx-muted mt-2">{{ $value['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="rx-section section-tone-light" data-rx-scene data-scene-id="about-capabilities"
        data-scene-color="#45C8FF" data-scene-intensity="1.06" data-scene-speed="1.02">
        <div class="rx-container relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                <div class="lg:col-span-4">
                    <span class="rx-eyebrow rx-reveal">// CAPABILITIES</span>

                    <h2 class="rx-h1 mt-4 rx-reveal">
                        نقش‌هایی که پشت
                        <span class="rx-gradient-text">رندیکس</span> ایستادن
                    </h2>

                    <p class="rx-body rx-muted mt-6 rx-reveal">
                        ما تیم بزرگی نیستیم، ولی هر نقش با دقت و مسئولیت full-stack تعریف شده.
                        همین باعث می‌شه هماهنگی بالا و کیفیت خروجی پایدار باشه.
                    </p>
                </div>

                <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($stack as $group)
                        <div class="rx-panel p-6 rx-reveal">
                            <div class="rx-card-content">
                                <div class="rx-icon-tile mb-5"
                                    style="width:3rem;height:3rem;border-radius:1rem;display:inline-flex;align-items:center;justify-content:center;color:var(--rx-section-accent);background:var(--rx-surface);border:1px solid var(--rx-border);font-size:1.15rem;">
                                    <i class="{{ $group['icon'] }}"></i>
                                </div>

                                <h3 class="rx-h3">{{ $group['title'] }}</h3>

                                <div class="rx-stack mt-4">
                                    @foreach ($group['items'] as $item)
                                        <span class="rx-chip">{{ $item }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="rx-section section-tone-primary" data-rx-scene data-scene-id="about-cta" data-scene-color="#1769FF"
        data-scene-intensity="1.15" data-scene-speed="1.08">
        <div class="rx-container relative z-10">
            <div class="rx-cta rx-reveal">
                <div class="rx-cta-inner p-8 md:p-14 text-center">
                    <span class="rx-badge mb-6">
                        <i class="fa-solid fa-paper-plane"></i>
                        قدم بعدی
                    </span>

                    <h2 class="rx-h1">
                        بیا ببینیم
                        <span class="rx-gradient-text">کسب‌وکار تو</span>
                        چطور می‌تونه بدرخشه
                    </h2>

                    <p class="rx-body-lg rx-muted mt-6 max-w-2xl mx-auto">
                        یک گفتگوی کوتاه کافی‌ه تا بفهمی رندیکس می‌تونه مسیر درست برای تو باشه یا نه.
                    </p>

                    <a href="/contact" wire:navigate class="rx-btn rx-btn-primary mt-10">
                        <span>رزرو مشاوره رایگان</span>
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>