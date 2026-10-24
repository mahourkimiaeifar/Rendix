<?php

use App\Models\Service;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public function services()
    {
        try {
            return Service::query()
                ->active()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }
};

?>

@php
    $services = $this->services();
    $siteName = setting('site.name', 'رندیکس');
    $telegram = setting('social.telegram', 'https://t.me/RendixDMServices');
    $contactPhone = setting('contact.phone');
    $contactPhoneTel = preg_replace('/\D/', '', (string) $contactPhone);
@endphp

<div>
    <section class="pt-36 pb-24 px-5 md:px-10">
        <div class="max-w-7xl mx-auto">

            {{-- هدر --}}
            <div class="max-w-3xl space-y-6 mb-16">
                <div class="badge fade-up">
                    <span class="badge-dot"></span>
                    خدمات رندیکس
                </div>

                <h1 class="text-[40px] md:text-6xl font-black leading-[1.2] fade-up delay-1">
                    هر خدمت،
                    <br>
                    <span class="gradient-text">یک مسیر رشد</span>
                </h1>

                <p class="text-lg text-snow/60 leading-9 fade-up delay-2">
                    از طراحی وبسایت گرفته تا سئو، دیجیتال مارکتینگ و پشتیبانی؛ همه‌چیز با هدف دیده شدن، اعتماد گرفتن و فروش بیشتر.
                </p>
            </div>

            @if ($services->isEmpty())
                <div class="glass p-7 md:p-10 max-w-3xl mx-auto text-center space-y-6">
                    <div class="text-5xl text-sky">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>

                    <h2 class="text-2xl md:text-3xl font-black">
                        هنوز خدمتی منتشر نشده است
                    </h2>

                    <p class="text-snow/60 leading-8">
                        به‌زودی خدمات {{ $siteName }} از پنل مدیریت منتشر می‌شود. برای درخواست سریع، همین حالا با ما در تماس باش.
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
                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($services as $service)
                        <article class="glass p-6 md:p-7 space-y-5 hover:border-sky/30 transition-all">
                            @if ($service->image_path)
                                <div class="rounded-2xl overflow-hidden border border-white/10 bg-white/5">
                                    <img
                                        src="{{ $service->imageUrl() }}"
                                        alt="{{ $service->title }}"
                                        class="w-full h-48 object-cover"
                                        loading="lazy"
                                    >
                                </div>
                            @else
                                <div class="icon-tile !w-14 !h-14 !text-2xl">
                                    <i class="{{ $service->icon ?: 'fa-solid fa-briefcase' }}"></i>
                                </div>
                            @endif

                            <div class="space-y-3">
                                <h2 class="text-xl md:text-2xl font-black leading-8">
                                    {{ $service->title }}
                                </h2>

                                <p class="text-sm text-snow/60 leading-7">
                                    {{ $service->short_description ?: 'برای دریافت جزئیات بیشتر، این خدمت را انتخاب کن.' }}
                                </p>
                            </div>

                            @if ($service->metric_value || $service->metric_label)
                                <div class="rounded-xl bg-white/5 border border-white/10 px-4 py-3 flex items-center justify-between gap-3">
                                    <strong class="text-sky text-lg">
                                        {{ $service->metric_value }}
                                    </strong>

                                    <span class="text-xs text-snow/50 font-bold">
                                        {{ $service->metric_label }}
                                    </span>
                                </div>
                            @endif

                            <a href="{{ route('services.show', $service->slug) }}" class="btn btn-primary w-full">
                                مشاهده جزئیات
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
                        </article>
                    @endforeach
                </div>

                <div class="mt-14 glass p-7 md:p-9 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="space-y-2">
                        <h3 class="text-xl md:text-2xl font-black">
                            هنوز مطمئن نیستی کدام خدمت برای کسب‌وکارت مناسب است؟
                        </h3>

                        <p class="text-sm text-snow/60 leading-7">
                            یک پیام بده یا تماس بگیر؛ بر اساس هدف، بودجه و زمانت راهنمایی‌ات می‌کنیم.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('contact') }}" class="btn btn-primary">
                            <i class="fa-solid fa-paper-plane"></i>
                            درخواست مشاوره
                        </a>

                        @if ($telegram)
                            <a href="{{ $telegram }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                                <i class="fa-brands fa-telegram"></i>
                                تلگرام
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>