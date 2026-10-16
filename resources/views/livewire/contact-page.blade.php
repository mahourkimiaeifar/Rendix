<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Support\Schema;
use Illuminate\Support\Facades\Log;

new #[Layout('components.layouts.app')] class extends Component {
    public $name = '';
    public $email = '';
    public $phone = '';
    public $service = '';
    public $message = '';
    public $sent = false;

    protected $rules = [
        'name' => 'required|string|min:3|max:120',
        'email' => 'required|email|max:190',
        'phone' => 'required|string|min:10|max:20',
        'service' => 'required|string|in:web-design,seo,digital-marketing,social-media,ecommerce,support,other',
        'message' => 'required|string|min:20|max:3000',
    ];

    public function messages(): array
    {
        return [
            'name.required' => 'لطفاً نامت رو وارد کن.',
            'name.min' => 'نام باید حداقل ۳ حرف باشه.',
            'email.required' => 'ایمیل رو فراموش نکن.',
            'email.email' => 'یک ایمیل معتبر وارد کن.',
            'phone.required' => 'شماره تماس لازم‌ه تا بتونیم سریع پاسخ بدیم.',
            'phone.min' => 'شماره تماس معتبر وارد کن.',
            'service.required' => 'بگو چه خدمتی برات اولویت داره.',
            'message.required' => 'کمی از پروژه یا سوالت بنویس.',
            'message.min' => 'پیام باید حداقل ۲۰ کاراکتر باشه.',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        Log::info('Rendix contact lead received', [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'service' => $this->service,
            'message' => $this->message,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->reset(['name', 'email', 'phone', 'service', 'message']);
        $this->sent = true;
    }

    public function rendering($view)
    {
        $view->layoutData([
            'title' => 'تماس با رندیکس',
            'metaDescription' => 'برای شروع پروژه، مشاوره یا هر سوالی با رندیکس در تماس باش. تیم رندیکس سریع پاسخ می‌دهد و مسیر رشد دیجیتال تو را شفاف بررسی می‌کند.',
            'keywords' => 'رندیکس',
            'ogImage' => '/images/og-contact.jpg',
            'schema' => [Schema::contact()],
        ]);
    }
}; ?>

@php
    $serviceOptions = [
        'web-design' => 'طراحی وبسایت',
        'seo' => 'سئو',
        'digital-marketing' => 'دیجیتال مارکتینگ',
        'social-media' => 'شبکه‌های اجتماعی',
        'ecommerce' => 'فروشگاه اینترنتی',
        'support' => 'پشتیبانی سایت',
        'other' => 'سایر / نمی‌دونم',
    ];

    $nextSteps = [
        [
            'icon' => 'fa-solid fa-envelope-open-text',
            'title' => 'ثبت درخواست',
            'text' => 'پیام تو در سیستم رندیکس ثبت می‌شه و بررسی اولیه انجام می‌شه.',
        ],
        [
            'icon' => 'fa-solid fa-comments',
            'title' => 'گفتگوی کوتاه',
            'text' => 'برای درک بهتر هدف، بودجه و زمان‌بندی، یک تماس یا گفتگوی کوتاه داریم.',
        ],
        [
            'icon' => 'fa-solid fa-list-check',
            'title' => 'پیشنهاد شفاف',
            'text' => 'مسیر کار، هزینه تقریبی، زمان تحویل و خروجی مورد انتظار به‌صورت شفاف ارائه می‌شه.',
        ],
    ];
@endphp

<div class="relative">

    <section
        class="rx-section rx-hero rx-hero-xs flex items-center section-tone-light"
        data-rx-scene
        data-scene-id="contact-hero"
        data-scene-color="#45C8FF"
        data-scene-intensity="1.05"
        data-scene-speed="1"
    >
        <div class="rx-container relative z-10">
            <div class="max-w-3xl">
                <span class="rx-eyebrow rx-reveal">// CONTACT</span>

                <h1 class="rx-display mt-6 rx-reveal">
                    با
                    <span class="rx-gradient-text">رندیکس</span>
                    در ارتباط باش
                </h1>

                <p class="rx-body-lg rx-muted mt-7 rx-reveal">
                    فرم رو پر کن یا مستقیم تماس بگیر.  
                    اولین پاسخ معمولاً سریع داده می‌شه، چون می‌دونیم وقت تو ارزشمنده.
                </p>
            </div>
        </div>
    </section>

    <section
        class="rx-section pt-0 section-tone-primary"
        data-rx-scene
        data-scene-id="contact-form"
        data-scene-color="#1769FF"
        data-scene-intensity="1.18"
        data-scene-speed="0.92"
    >
        <div class="rx-container relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-7">

                <div class="lg:col-span-4 space-y-5">
                    <div class="rx-panel p-7 rx-reveal">
                        <div class="rx-card-content">
                            <h2 class="rx-h3 mb-5">راه‌های ارتباطی</h2>

                            <ul class="space-y-5">
                                <li class="flex items-start gap-3">
                                    <i class="fa-solid fa-phone mt-1 rx-text-primary"></i>
                                    <div>
                                        <p class="rx-caption rx-soft">تلفن</p>
                                        <a href="tel:+989000000000" class="rx-body font-bold hover:rx-text-light transition-colors rx-breakable">
                                            ۰۹۰-۰۰۰-۰۰۰۰
                                        </a>
                                    </div>
                                </li>

                                <li class="flex items-start gap-3">
                                    <i class="fa-solid fa-envelope mt-1 rx-text-purple"></i>
                                    <div>
                                        <p class="rx-caption rx-soft">ایمیل</p>
                                        <a href="mailto:hello@rendix.ir" class="rx-body font-bold hover:rx-text-light transition-colors rx-breakable">
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
                        </div>
                    </div>

                    <div class="rx-panel p-7 rx-reveal">
                        <div class="rx-card-content">
                            <h2 class="rx-h3 mb-4">چه چیزی بفرستیم؟</h2>

                            <ul class="space-y-3 rx-body rx-muted">
                                <li class="flex items-start gap-2">
                                    <i class="fa-solid fa-check mt-1 rx-text-primary"></i>
                                    <span>مشاوره اولیه رایگان</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fa-solid fa-check mt-1 rx-text-primary"></i>
                                    <span>بررسی سریع وبسایت فعلی</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fa-solid fa-check mt-1 rx-text-primary"></i>
                                    <span>پیشنهاد مسیر رشد دیجیتال</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fa-solid fa-check mt-1 rx-text-primary"></i>
                                    <span>تخمین زمان و هزینه شفاف</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-8">
                    <div class="rx-panel p-7 md:p-9 rx-reveal">
                        <div class="rx-card-content">

                            @if ($sent)
                                <div class="text-center py-12">
                                    <div class="w-20 h-20 rounded-full mx-auto mb-6 flex items-center justify-center text-3xl"
                                         style="background: rgba(23,105,255,0.12); border: 1px solid rgba(23,105,255,0.28); color: var(--rx-text-primary);">
                                        <i class="fa-solid fa-check"></i>
                                    </div>

                                    <h2 class="rx-h2">پیامت ثبت شد</h2>

                                    <p class="rx-body-lg rx-muted mt-4 max-w-xl mx-auto">
                                        ممنون که به رندیکس اعتماد کردی.  
                                        تیم ما در اسرع وقت بررسی می‌کنه و باهات تماس می‌گیره.
                                    </p>

                                    <button type="button" wire:click="$set('sent', false)" class="rx-btn rx-btn-outline mt-8">
                                        <span>ارسال پیام جدید</span>
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                </div>
                            @else
                                <form wire:submit="submit" class="space-y-5">
                                    <div>
                                        <h2 class="rx-h2">فرم مشاوره</h2>
                                        <p class="rx-body rx-muted mt-3">
                                            اطلاعاتت رو وارد کن. هرچه دقیق‌تر بگی، پیشنهاد ما بهتر خواهد بود.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="name" class="rx-label">نام و نام خانوادگی</label>
                                            <input
                                                id="name"
                                                type="text"
                                                wire:model.blur="name"
                                                class="rx-input"
                                                placeholder="مثلاً ماهور کیمیایی‌فر"
                                            >
                                            @error('name') <p class="rx-error">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label for="email" class="rx-label">ایمیل</label>
                                            <input
                                                id="email"
                                                type="email"
                                                wire:model.blur="email"
                                                class="rx-input"
                                                placeholder="you@example.com"
                                                dir="ltr"
                                            >
                                            @error('email') <p class="rx-error">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label for="phone" class="rx-label">شماره تماس</label>
                                            <input
                                                id="phone"
                                                type="tel"
                                                wire:model.blur="phone"
                                                class="rx-input"
                                                placeholder="۰۹۱۲۳۴۵۶۷۸۹"
                                                dir="ltr"
                                            >
                                            @error('phone') <p class="rx-error">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label for="service" class="rx-label">خدمت مورد نظر</label>
                                            <select id="service" wire:model.blur="service" class="rx-input">
                                                <option value="">انتخاب کن...</option>
                                                @foreach ($serviceOptions as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('service') <p class="rx-error">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="message" class="rx-label">درباره پروژه یا سوالت بنویس</label>
                                        <textarea
                                            id="message"
                                            wire:model.blur="message"
                                            rows="6"
                                            class="rx-input resize-none"
                                            placeholder="مثلاً: یک فروشگاه اینترنتی می‌خوام که سرعت بالا و سئوی خوبی داشته باشه..."
                                        ></textarea>
                                        @error('message') <p class="rx-error">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="flex flex-wrap items-center justify-between gap-4 pt-1">
                                        <p class="rx-caption rx-soft max-w-md">
                                            اطلاعاتت فقط برای بررسی پروژه استفاده می‌شه و اسپم نمی‌فرستیم.
                                        </p>

                                        <button type="submit" class="rx-btn rx-btn-primary" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="submit">ارسال درخواست</span>
                                            <span wire:loading wire:target="submit">در حال ارسال...</span>
                                            <i class="fa-solid fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </form>
                            @endif

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section
        class="rx-section rx-section-alt section-tone-purple"
        data-rx-scene
        data-scene-id="contact-next"
        data-scene-color="#7653F6"
        data-scene-intensity="1.08"
        data-scene-speed="0.95"
    >
        <div class="rx-container relative z-10">
            <div class="max-w-3xl mx-auto text-center mb-14">
                <span class="rx-eyebrow rx-reveal">// NEXT STEPS</span>

                <h2 class="rx-h1 mt-4 rx-reveal">
                    بعد از ارسال پیام
                    <span class="rx-gradient-text">چه اتفاقی می‌افته؟</span>
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($nextSteps as $step)
                    <div class="rx-panel p-7 rx-reveal">
                        <div class="rx-card-content">
                            <div class="rx-icon-tile mb-5" style="width:3rem;height:3rem;border-radius:1rem;display:inline-flex;align-items:center;justify-content:center;color:var(--rx-section-accent);background:var(--rx-surface);border:1px solid var(--rx-border);font-size:1.15rem;">
                                <i class="{{ $step['icon'] }}"></i>
                            </div>

                            <h3 class="rx-h3">{{ $step['title'] }}</h3>
                            <p class="rx-body rx-muted mt-3">{{ $step['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</div>