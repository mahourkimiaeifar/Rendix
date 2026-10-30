<?php

use App\Models\Project;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = trim($slug);
    }

    public function project(): ?Project
    {
        try {
            return Project::query()->active()->where('slug', $this->slug)->first();
        } catch (\Throwable $e) {
            return null;
        }
    }
};

?>

@php
    $project = $this->project();

    if (! $project) {
        abort(404);
    }

    $siteName = setting('site.name', 'رندیکس');

    $pageTitle = $project->meta_title ?: ($project->title . ' | ' . $siteName);
    $pageDescription = $project->meta_description ?: ($project->short_description ?: $project->title);
@endphp

@section('rendix.title', $pageTitle)
@section('rendix.description', $pageDescription)
@section('rendix.canonical', route('projects.show', $project->slug))
@section('rendix.image', $project->image_path ? $project->imageUrl() : setting_url('brand.og_image', setting_url('brand.favicon', asset('images/favicon.png?v=6'))))
@section('rendix.type', 'article')

@push('rendix.head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $project->title,
            'description' => $pageDescription,
            'url' => route('projects.show', $project->slug),
            'image' => $project->image_path ? $project->imageUrl() : null,
            'creator' => ['@type' => 'Organization', 'name' => $siteName],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

<div>
    <section class="rx-section tight" style="padding-top: 130px;">
        <div class="rx-container">
            <nav class="rx-breadcrumb-public" aria-label="مسیر صفحه">
                <a href="{{ route('home') }}" wire:navigate>خانه</a>
                <i class="fa-solid fa-chevron-left"></i>
                <a href="{{ route('projects') }}" wire:navigate>نمونه‌کارها</a>
                <i class="fa-solid fa-chevron-left"></i>
                <strong>{{ $project->title }}</strong>
            </nav>

            <div class="rx-project-detail-head">
                <span class="rx-project-category large">{{ $project->categoryLabel() }}</span>

                <h1 class="fade-up delay-1" style="margin:0;font-size:clamp(30px,4.5vw,52px);font-weight:950;line-height:1.25;">
                    {{ $project->title }}
                </h1>

                @if ($project->short_description)
                    <p class="rx-project-lede fade-up delay-2">{{ $project->short_description }}</p>
                @endif
            </div>

            @if ($project->image_path)
                <div class="rx-project-hero rv">
                    <img src="{{ $project->imageUrl() }}" alt="{{ $project->title }}">
                </div>
            @endif

            <div class="rx-project-detail-grid">
                <div class="rx-project-main">
                    @if ($project->challenge)
                        <div class="rx-project-block rv">
                            <h2><i class="fa-solid fa-mountain"></i> چالش</h2>
                            <p>{{ $project->challenge }}</p>
                        </div>
                    @endif

                    @if ($project->solution)
                        <div class="rx-project-block rv">
                            <h2><i class="fa-solid fa-wand-magic-sparkles"></i> راه‌حل رندیکس</h2>
                            <p>{{ $project->solution }}</p>
                        </div>
                    @endif

                    @if ($project->result)
                        <div class="rx-project-block rv">
                            <h2><i class="fa-solid fa-chart-line"></i> نتیجه</h2>
                            <p>{{ $project->result }}</p>
                        </div>
                    @endif

                    @if ($project->galleryUrls())
                        <div class="rx-project-block rv">
                            <h2><i class="fa-solid fa-images"></i> گالری</h2>

                            <div class="rx-project-gallery">
                                @foreach ($project->galleryUrls() as $url)
                                    <img src="{{ $url }}" alt="گالری {{ $project->title }}" loading="lazy">
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <aside class="rx-project-side">
                    <div class="rx-project-info-card rv">
                        <h3>اطلاعات پروژه</h3>

                        <div class="rx-project-info-list">
                            @if ($project->client_name)
                                <div>
                                    <span>مشتری</span>
                                    <strong>{{ $project->client_name }}</strong>
                                </div>
                            @endif

                            <div>
                                <span>دسته‌بندی</span>
                                <strong>{{ $project->categoryLabel() }}</strong>
                            </div>

                            @if ($project->delivered_at)
                                <div>
                                    <span>تاریخ تحویل</span>
                                    <strong>{{ Jalali::date($project->delivered_at) }}</strong>
                                </div>
                            @endif

                            @if ($project->technologyList())
                                <div>
                                    <span>تکنولوژی‌ها</span>
                                    <div class="rx-tech-tags" style="margin-top:8px;">
                                        @foreach ($project->technologyList() as $tech)
                                            <span>{{ $tech }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if ($project->demo_url)
                            <a
                                href="{{ $project->demo_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="rx-project-demo-btn"
                            >
                                <i class="fa-solid fa-up-right-from-square"></i>
                                مشاهده دمو
                            </a>
                        @endif

                        <a href="{{ route('contact', ['service' => $project->category]) }}" class="rx-project-cta-btn" wire:navigate>
                            <i class="fa-solid fa-rocket"></i>
                            مشابه این پروژه می‌خواهم
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <x-site.testimonials :limit="3" title="مشتری‌ها چه می‌گویند؟" subtitle="بازخورد واقعی کسانی که با رندیکس رشد کرده‌اند." />

    <x-site.cta-band
        title="داستان موفقیت بعدی را تو بنویس"
        text="از چالش تا نتیجه؛ ما کنار تو هستیم. همین حالا پروژه‌ات را شروع کن."
    />
</div>