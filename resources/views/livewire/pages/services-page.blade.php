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
@endphp

@section('rendix.title', 'خدمات | ' . $siteName)
@section('rendix.description', 'طراحی وبسایت، سئو، دیجیتال مارکتینگ، فروشگاه اینترنتی و پشتیبانی؛ خدمات رندیکس برای رشد واقعی کسب‌وکار شما.')
@section('rendix.canonical', route('services'))
@section('rendix.type', 'WebPage')

<div>
    <x-site.page-hero
        badge="خدمات رندیکس"
        title="هر خدمت،"
        gradient="یک مسیر رشد."
        lede="از طراحی وبسایت گرفته تا سئو، دیجیتال مارکتینگ و پشتیبانی؛ همه‌چیز با هدف دیده شدن، اعتماد گرفتن و فروش بیشتر."
    />

    <section class="rx-section tight">
        <div class="rx-container">
            @if ($services->isEmpty())
                <x-site.empty-state
                    icon="fa-solid fa-layer-group"
                    title="هنوز خدمتی منتشر نشده است"
                    text="به‌زودی خدمات رندیکس از پنل مدیریت منتشر می‌شود؛ برای شروع همکاری همین حالا پیام بده."
                />
            @else
                <div class="rx-services-grid">
                    @foreach ($services as $service)
                        <article class="rx-service-card rv">
                            @if ($service->image_path)
                                <img
                                    src="{{ $service->imageUrl() }}"
                                    alt="{{ $service->title }}"
                                    class="rx-service-image"
                                    loading="lazy"
                                >
                            @endif

                            <div class="rx-service-body">
                                <i class="{{ $service->icon ?: 'fa-solid fa-briefcase' }} rx-service-icon"></i>

                                <h3>{{ $service->title }}</h3>

                                <p>{{ $service->short_description ?: 'برای دیدن جزئیات، مسیر اجرا و نمونه‌های مرتبط، این خدمت را باز کن.' }}</p>

                                @if ($service->metric_value || $service->metric_label)
                                    <div class="rx-service-metric">
                                        <strong>{{ $service->metric_value }}</strong>
                                        <span>{{ $service->metric_label }}</span>
                                    </div>
                                @endif

                                <a href="{{ route('services.show', $service->slug) }}" class="rx-service-link">
                                    مشاهده خدمت
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <x-site.cta-band
        title="نمی‌دانی کدام خدمت برای تو است؟"
        text="یک پیام بده؛ بر اساس هدف، بودجه و زمانت، کوتاه‌ترین مسیر رشد را پیشنهاد می‌دهیم."
    />
</div>