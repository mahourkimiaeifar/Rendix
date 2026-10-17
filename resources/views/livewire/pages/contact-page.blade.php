<?php

use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|string|min:3|max:120')]
    public string $name = '';

    #[Validate('required|email|max:190')]
    public string $email = '';

    #[Validate('required|string|min:10|max:20')]
    public string $phone = '';

    #[Validate('required|string')]
    public string $service = '';

    #[Validate('required|string|min:20|max:3000')]
    public string $message = '';

    public bool $sent = false;

    public function submit(): void
    {
        $this->validate();

        Log::info('Rendix contact lead received', [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'service' => $this->service,
            'message' => $this->message,
        ]);

        $this->sent = true;
        $this->reset('name', 'email', 'phone', 'service', 'message');
    }
};

?>

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
                                <input type="tel" wire:model="phone" placeholder="۰۹۱۲ ۳۴۵ ۶۷۸۹"
                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors">
                                @error('phone') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-snow/70">کدوم خدمت رو می‌خوای؟ *</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @php
                                    $services = [
                                        'web-design' => 'طراحی وبسایت',
                                        'seo' => 'سئو',
                                        'digital-marketing' => 'مارکتینگ',
                                        'social-media' => 'شبکه اجتماعی',
                                        'ecommerce' => 'فروشگاه',
                                        'support' => 'پشتیبانی',
                                        'other' => 'نمی‌دونم، راهنماییم کن',
                                    ];
                                @endphp
                                @foreach($services as $value => $label)
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

                        @if($sent ?? false)
                            <div
                                class="bg-emerald-500/10 border border-emerald-500/30 rounded-xl p-4 text-emerald-300 text-sm flex items-center gap-3">
                                <i class="fa-solid fa-circle-check"></i>
                                پیامت رسید! خیلی زود باهات تماس می‌گیریم. مرسی که به ما اعتماد کردی. 🙏
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
                            <a href="mailto:info@rendix.ir" class="flex items-center gap-4 group">
                                <div class="icon-tile !w-12 !h-12 !text-lg"><i class="fa-solid fa-envelope"></i></div>
                                <div>
                                    <div class="text-xs text-snow/40 mb-0.5">ایمیل</div>
                                    <div class="text-sm font-bold group-hover:text-sky transition-colors" dir="ltr">
                                        info@rendix.ir</div>
                                </div>
                            </a>
                            <a href="tel:+989123456789" class="flex items-center gap-4 group">
                                <div class="icon-tile icon-tile-violet !w-12 !h-12 !text-lg"><i
                                        class="fa-solid fa-phone"></i></div>
                                <div>
                                    <div class="text-xs text-snow/40 mb-0.5">تلفن</div>
                                    <div class="text-sm font-bold group-hover:text-sky transition-colors">۰۹۱۲ ۳۴۵ ۶۷۸۹
                                    </div>
                                </div>
                            </a>
                            <div class="flex items-center gap-4">
                                <div class="icon-tile !w-12 !h-12 !text-lg"><i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-snow/40 mb-0.5">آدرس</div>
                                    <div class="text-sm font-bold">نجف‌آباد، اصفهان، ایران</div>
                                </div>
                            </div>
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
                            @foreach($next as $item)
                                <div class="flex gap-4">
                                    <span
                                        class="w-8 h-8 rounded-lg bg-gradient-to-l from-primary to-sky flex items-center justify-center text-sm font-black flex-shrink-0">{{ $item['n'] }}</span>
                                    <div>
                                        <div class="text-sm font-bold mb-0.5">{{ $item['title'] }}</div>
                                        <div class="text-xs text-snow/45 leading-6">{{ $item['desc'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>