<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')]
    class extends Component {
    //
};

?>

@php
    $siteName = setting('site.name', 'رندیکس');
    $pageTitle = 'درباره ما | ' . $siteName;
    $pageDescription = 'رندیکس یک آژانس خلاقیت دیجیتال است؛ ایده‌ها را به تجربه‌های دیجیتال تبدیل می‌کنیم.';
@endphp

@section('rendix.title', $pageTitle)
@section('rendix.description', $pageDescription)
@section('rendix.canonical', route('about'))
@section('rendix.type', 'AboutPage')

<div>
    <x-site.page-hero
        badge="درباره رندیکس"
        title="ما فقط وبسایت نمی‌سازیم؛"
        gradient="مسیر رشد می‌سازیم."
        lede="رندیکس یک آژانس خلاقیت دیجیتال است که ایده‌ها را به تجربه‌های دیجیتال تبدیل می‌کند؛ با طراحی وبسایت، سئو، محتوا و تجربه‌های تعاملی و سه‌بعدی."
    />

    {{-- داستان --}}
    <section class="rx-section tight">
        <div class="rx-container">
            <div class="grid md:grid-cols-2 gap-6">
                <div class="glass p-7 md:p-9 space-y-4 rv">
                    <div class="icon-tile"><i class="fa-solid fa-seedling"></i></div>
                    <h3 class="text-2xl font-black">از یک ایده کوچک در نجف‌آباد</h3>
                    <p class="text-snow/60 leading-8">
                        رندیکس از این باور شروع شد که کسب‌وکارهای ایرانی لایق وبسایت‌هایی هستند که هم زیبا باشند، هم بفروشند.
                        نه قالب‌های تکراری؛ بلکه تجربه‌هایی با هویت، سرعت و هدف.
                    </p>
                </div>

                <div class="glass p-7 md:p-9 space-y-4 rv">
                    <div class="icon-tile icon-tile-violet"><i class="fa-solid fa-compass"></i></div>
                    <h3 class="text-2xl font-black">به یک قطب‌نمای رشد تبدیل شد</h3>
                    <p class="text-snow/60 leading-8">
                        امروز رندیکس با ترکیب طراحی، سئو، دیجیتال مارکتینگ و تجربه‌های تعاملی،
                        به کسب‌وکارها کمک می‌کند دیده شوند، اعتماد بسازند و بفروشند.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- آمار --}}
    <section class="rx-section tight">
        <div class="rx-container">
            <div class="glass p-7 md:p-9 rv">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 text-center">
                    <div class="space-y-2">
                        <strong class="block text-4xl font-black text-sky" data-count="48" data-suffix="+">۰</strong>
                        <span class="text-sm text-snow/55 font-bold">پروژه تحویل‌شده</span>
                    </div>
                    <div class="space-y-2">
                        <strong class="block text-4xl font-black text-sky" data-count="32">۰</strong>
                        <span class="text-sm text-snow/55 font-bold">مشتری فعال</span>
                    </div>
                    <div class="space-y-2">
                        <strong class="block text-4xl font-black text-sky" data-count="6">۰</strong>
                        <span class="text-sm text-snow/55 font-bold">سال تجربه</span>
                    </div>
                    <div class="space-y-2">
                        <strong class="block text-4xl font-black text-sky" data-count="98" data-suffix="٪">۰</strong>
                        <span class="text-sm text-snow/55 font-bold">رضایت مشتریان</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ارزش‌ها --}}
    <section class="rx-section tight">
        <div class="rx-container">
            <div class="rx-section-head">
                <h2>ارزش‌هایی که کوتاه‌شان نمی‌کنیم</h2>
                <p>چهار اصلی که پشت هر پروژه رندیکس ایستاده‌اند.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="glass glass-hover p-6 space-y-3 rv">
                    <div class="icon-tile"><i class="fa-solid fa-eye"></i></div>
                    <h3 class="text-lg font-black">شفافیت</h3>
                    <p class="text-sm text-snow/55 leading-7">برنامه، هزینه و وضعیت پروژه همیشه روشن است؛ بدون ابهام.</p>
                </div>

                <div class="glass glass-hover p-6 space-y-3 rv">
                    <div class="icon-tile"><i class="fa-solid fa-bolt"></i></div>
                    <h3 class="text-lg font-black">سرعت</h3>
                    <p class="text-sm text-snow/55 leading-7">هم در تحویل، هم در پاسخ‌گویی، هم در عملکرد خود وبسایت.</p>
                </div>

                <div class="glass glass-hover p-6 space-y-3 rv">
                    <div class="icon-tile icon-tile-violet"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                    <h3 class="text-lg font-black">خلاقیت</h3>
                    <p class="text-sm text-snow/55 leading-7">طراحی‌هایی که دیده می‌شوند و در ذهن می‌مانند؛ نه قالب‌های تکراری.</p>
                </div>

                <div class="glass glass-hover p-6 space-y-3 rv">
                    <div class="icon-tile"><i class="fa-solid fa-bullseye"></i></div>
                    <h3 class="text-lg font-black">نتیجه‌گرایی</h3>
                    <p class="text-sm text-snow/55 leading-7">زیبایی بدون تبدیل کافی نیست؛ هدف ما رشد واقعی کسب‌وکار توست.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- فرآیند --}}
    <section class="rx-section tight">
        <div class="rx-container">
            <div class="rx-section-head">
                <h2>مسیر همکاری با ما</h2>
                <p>چهار قدم ساده از اولین سلام تا رشد مستمر.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @php
                    $steps = [
                        ['n' => '۰۱', 't' => 'گفتگوی اولیه', 'd' => 'نیاز، هدف و بودجه را می‌شنویم.', 'i' => 'fa-solid fa-comments'],
                        ['n' => '۰۲', 't' => 'نقشه مسیر', 'd' => 'پیشنهاد شفاف با زمان‌بندی مشخص.', 'i' => 'fa-solid fa-map'],
                        ['n' => '۰۳', 't' => 'اجرا و تحویل', 'd' => 'طراحی، توسعه، تست و انتشار.', 'i' => 'fa-solid fa-rocket'],
                        ['n' => '۰۴', 't' => 'رشد مستمر', 'd' => 'سئو، بهینه‌سازی و پشتیبانی.', 'i' => 'fa-solid fa-chart-line'],
                    ];
                @endphp

                @foreach ($steps as $step)
                    <div class="glass glass-hover p-6 space-y-3 rv">
                        <div class="flex items-center justify-between">
                            <div class="icon-tile !w-12 !h-12 !text-lg"><i class="{{ $step['i'] }}"></i></div>
                            <span class="text-2xl font-black text-snow/20">{{ $step['n'] }}</span>
                        </div>
                        <h3 class="text-lg font-black">{{ $step['t'] }}</h3>
                        <p class="text-sm text-snow/55 leading-7">{{ $step['d'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <x-site.cta-band
        title="داستان رشد تو از اینجا شروع می‌شود"
        text="یک پیام بده؛ ما گوش می‌دهیم و مسیر را با هم می‌چینیم."
    />
</div>