<?php

use App\Models\Lead;
use App\Models\Service;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')]
    class extends Component {
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $service = '';

    public string $message = '';

    public bool $sent = false;

    public function mount(): void
    {
        $requested = (string) request('service');

        if ($requested !== '' && array_key_exists($requested, $this->serviceOptions())) {
            $this->service = $requested;
        }
    }

    public function serviceOptions(): array
    {
        try {
            $options = Service::query()
                ->active()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('title', 'slug')
                ->all();
        } catch (\Throwable $e) {
            $options = [];
        }

        return $options !== []
            ? $options
            : Lead::serviceOptions();
    }

    public function submit(): void
    {
        if (!(bool) setting('contact.enabled', true)) {
            return;
        }

        $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'service' => [
                'required',
                'string',
                'in:' . implode(',', array_keys($this->serviceOptions())),
            ],
            'message' => ['required', 'string', 'min:20', 'max:3000'],
        ], [
            'name.required' => 'لطفاً اسمت رو بنویس.',
            'name.min' => 'نام باید حداقل ۳ کاراکتر باشد.',
            'name.max' => 'نام نمی‌تواند بیشتر از ۱۲ کاراکتر باشد.',

            'email.required' => 'ایمیل رو وارد کن.',
            'email.email' => 'ایمیل معتبر نیست.',
            'email.max' => 'ایمیل نمی‌تواند بیشتر از ۱۹ کاراکتر باشد.',

            'phone.required' => 'شماره تماس رو وارد کن.',
            'phone.min' => 'شماره تماس معتبر نیست.',
            'phone.max' => 'شماره تماس معتبر نیست.',

            'service.required' => 'یکی از خدمات رو انتخاب کن.',
            'service.in' => 'خدمت انتخابی معتبر نیست.',

            'message.required' => 'لطفاً پیام خودت رو بنویس.',
            'message.min' => 'پیام باید حداقل ۲۰ کاراکتر باشد.',
            'message.max' => 'پیام نمی‌تواند بیشتر از ۳۰۰ کاراکتر باشد.',
        ], [
            'name' => 'نام',
            'email' => 'ایمیل',
            'phone' => 'شماره تماس',
            'service' => 'خدمت مورد نظر',
            'message' => 'پیام',
        ]);

        $key = 'rendix-contact:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('message', 'برای جلوگیری از اسپم، لطفاً چند دقیقه صبر کن و دوباره تلاش کن.');

            return;
        }

        $lead = Lead::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'service' => $this->service,
            'message' => $this->message,
            'source_page' => '/' . ltrim(request()->path(), '/'),
            'status' => 'new',
            'priority' => 'normal',
            'metadata' => [
                'user_agent' => request()->userAgent(),
                'ip' => request()->ip(),
                'referer' => request()->header('referer'),
                'query' => request()->query(),
                'device' => str_contains((string) request()->userAgent(), 'Mobile') ? 'mobile' : 'desktop',
            ],
        ]);

        ActivityLogger::log('lead.created', $lead, [
            'service' => $lead->service,
            'source_page' => $lead->source_page,
        ]);

        RateLimiter::hit($key, 60 * 10);

        $this->sent = true;

        $this->reset('name', 'email', 'phone', 'service', 'message');
    }
};

?>

@php
    $contactEnabled = (bool) setting('contact.enabled', true);

    $serviceOptions = $this->serviceOptions();

    $successMessage = setting(
        'contact.success_message',
        'پیامت رسید! خیلی زود باهات تماس می‌گیریم. مرسی که به ما اعتماد کردی. 🙏'
    );

    $siteName = setting('site.name', 'رندیکس');

    $contactEmail = setting('contact.email') ?: 'info@rendix.ir';

    $contactPhone = setting('contact.phone') ?: '۰۹۱۲ ۳۴ ۶۷۸۹';
    $contactPhoneLatin = \App\Support\Jalali::toLatinDigits((string) $contactPhone);
    $contactPhoneTel = preg_replace('/\D/', '', $contactPhoneLatin);

    $contactAddress = setting('contact.address') ?: 'نجف‌آباد، اصفهان، ایران';

    $telegram = setting('social.telegram', 'https://t.me/RendixDMServices');
@endphp

<div>
    <section class="pt-36 pb-24 px-5 md:px-10">
        <div class="max-w-7xl mx-auto">

            {{-- هدر --}}
            <div class="max-w-3xl space-y-6 mb-16">
                <div class="badge fade-up">
                    <span class="badge-dot"></span>
                    هر پروژه‌ی بزرگ با یه سلام شروع میشه
                </div>

                <h1 class="text-[40px] md:text-6xl font-black leading-[1.2] fade-up delay-1">
                    حرفت رو بزن،
                    <br>
                    <span class="gradient-text">ما می‌شنویم.</span>
                </h1>

                <p class="text-lg text-snow/60 leading-9 fade-up delay-2">
                    فرم زیر رو پر کن یا مستقیم پیام بده. هر راهی که راحت‌تری. ما ظرف ۲۴ ساعت جواب می‌دیم.
                </p>
            </div>

            @if (!$contactEnabled)
                <div class="glass p-7 md:p-9 max-w-3xl mx-auto text-center space-y-5">
                    <div class="text-5xl text-amber-300">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>

                    <h2 class="text-2xl md:text-3xl font-black">
                        فرم تماس فعلاً غیرفعال است
                    </h2>

                    <p class="text-snow/60 leading-8">
                        برای ارتباط سریع‌تر، از طریق تلگرام یا تلفن با ما در تماس باش.
                    </p>

                    <div class="flex flex-wrap justify-center gap-3">
                        @if ($telegram)
                            <a href="{{ $telegram }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                                <i class="fa-brands fa-telegram"></i>
                                تلگرام
                            </a>
                        @endif

                        @if ($contactPhoneTel)
                            <a href="tel:{{ $contactPhoneTel }}" class="btn btn-primary">
                                <i class="fa-solid fa-phone"></i>
                                تماس بگیرید
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="grid lg:grid-cols-5 gap-10">

                    {{-- فرم --}}
                    <div class="lg:col-span-3">
                        <form wire:submit="submit" class="glass p-7 md:p-9 space-y-6">

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-snow/70">اسمت چیه؟ *</label>
                                <input type="text" wire:model="name" placeholder="مثلاً: علی رضایی"
                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors">
                                @error('name') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-snow/70">ایمیل *</label>
                                    <input type="email" wire:model="email" placeholder="you@example.com"
                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                                        dir="ltr">
                                    @error('email') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-snow/70">شماره تماس *</label>
                                    <input type="tel" wire:model="phone" placeholder="۰۹۲ ۳۴۵ ۶۷۹"
                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors">
                                    @error('phone') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-snow/70">کدوم خدمت رو می‌خوای؟ *</label>

                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach ($serviceOptions as $value => $label)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="service" value="{{ $value }}" wire:model="service"
                                                class="hidden peer">
                                            <div
                                                class="text-center text-xs md:text-sm border border-white/10 rounded-xl px-3 py-3 text-snow/60 peer-checked:border-sky peer-checked:bg-sky/10 peer-checked:text-sky transition-all">
                                                {{ $label }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                @error('service') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-snow/70">از کسب‌وکارت بگو *</label>
                                <textarea wire:model="message" rows="5"
                                    placeholder="چه کاری انجام می‌دی؟ چه هدفی داری؟ هر چقدر دوست داری بنویس..."
                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors resize-none leading-7"></textarea>
                                @error('message') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>

                            @if ($sent)
                                <div
                                    class="bg-emerald-500/10 border border-emerald-500/30 rounded-xl p-4 text-emerald-300 text-sm flex items-center gap-3">
                                    <i class="fa-solid fa-circle-check"></i>
                                    {{ $successMessage }}
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary w-full !py-4" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    ارسال پیام
                                    <i class="fa-solid fa-paper-plane"></i>
                                </span>
                                <span wire:loading>در حال ارسال...</span>
                            </button>
                        </form>
                    </div>

                    {{-- اطلاعات تماس --}}
                    <div class="lg:col-span-2 space-y-6">

                        <div class="glass p-7 space-y-6">
                            <h3 class="text-lg font-black">راه‌های دیگه</h3>

                            <div class="space-y-5">
                                @if ($contactEmail)
                                    <a href="mailto:{{ $contactEmail }}" class="flex items-center gap-4 group">
                                        <div class="icon-tile !w-12 !h-12 !text-lg">
                                            <i class="fa-solid fa-envelope"></i>
                                        </div>
                                        <div>
                                            <div class="text-xs text-snow/40 mb-0.5">ایمیل</div>
                                            <div class="text-sm font-bold group-hover:text-sky transition-colors" dir="ltr">
                                                {{ $contactEmail }}
                                            </div>
                                        </div>
                                    </a>
                                @endif

                                @if ($contactPhone)
                                    <a href="tel:{{ $contactPhoneTel }}" class="flex items-center gap-4 group">
                                        <div class="icon-tile icon-tile-violet !w-12 !h-12 !text-lg">
                                            <i class="fa-solid fa-phone"></i>
                                        </div>
                                        <div>
                                            <div class="text-xs text-snow/40 mb-0.5">تلفن</div>
                                            <div class="text-sm font-bold group-hover:text-sky transition-colors">
                                                {{ $contactPhone }}
                                            </div>
                                        </div>
                                    </a>
                                @endif

                                @if ($contactAddress)
                                    <div class="flex items-center gap-4">
                                        <div class="icon-tile !w-12 !h-12 !text-lg">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        <div>
                                            <div class="text-xs text-snow/40 mb-0.5">آدرس</div>
                                            <div class="text-sm font-bold">
                                                {{ $contactAddress }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- مراحل بعد --}}
                        <div class="glass p-7 space-y-5">
                            <h3 class="text-lg font-black">بعد از ارسال چی میشه؟</h3>

                            <div class="space-y-4">
                                @php
                                    $next = [
                                        ['n' => '۱', 'title' => 'جواب سریع', 'desc' => 'ظرف ۲۴ ساعت پیام می‌دی، جواب می‌گیری.'],
                                        ['n' => '۲', 'title' => 'گفتگوی کوتاه', 'desc' => 'یه تماس ۱۵ دقیقه‌ای برای شناخت بهتر.'],
                                        ['n' => '۳', 'title' => 'پیشنهاد شفاف', 'desc' => 'برنامه و هزینه مشخص، بدون ابهام.'],
                                    ];
                                @endphp

                                @foreach ($next as $item)
                                    <div class="flex gap-4">
                                        <span
                                            class="w-8 h-8 rounded-lg bg-gradient-to-l from-primary to-sky flex items-center justify-center text-sm font-black flex-shrink-0">
                                            {{ $item['n'] }}
                                        </span>
                                        <div>
                                            <div class="text-sm font-bold mb-0.5">{{ $item['title'] }}</div>
                                            <div class="text-xs text-snow/45 leading-6">{{ $item['desc'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <x-site.faq location="contact" />
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>