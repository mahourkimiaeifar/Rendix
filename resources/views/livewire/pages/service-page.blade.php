<?php

use App\Models\Project;
use App\Models\Service;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = trim($slug);
    }

    public function service(): ?Service
    {
        try {
            return Service::query()->active()->where('slug', $this->slug)->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function relatedProjects(?Service $service)
    {
        try {
            $query = Project::query()->active();

            $categoryExists = $service && array_key_exists($service->slug, Project::categoryOptions());

            if ($categoryExists) {
                $query->where('category', $service->slug);
            } else {
                $query->where('is_featured', true);
            }

            $projects = $query->orderBy('sort_order')->orderByDesc('id')->limit(3)->get();

            if ($projects->isEmpty() && $categoryExists) {
                $projects = Project::query()->active()->where('is_featured', true)
                    ->orderBy('sort_order')->orderByDesc('id')->limit(3)->get();
            }

            return $projects;
        } catch (\Throwable $e) {
            return collect();
        }
    }
};

?>

@php
    $service = $this->service();

    if (! $service) {
        abort(404);
    }

    $projects = $this->relatedProjects($service);
    $siteName = setting('site.name', 'رندیکس');

    $pageTitle = $service->meta_title ?: ($service->title . ' | ' . $siteName);
    $pageDescription = $service->meta_description ?: ($service->short_description ?: $service->title);
@endphp

@section('rendix.title', $pageTitle)
@section('rendix.description', $pageDescription)
@section('rendix.canonical', route('services.show', $service->slug))
@section('rendix.image', $service->image_path ? $service->imageUrl() : setting_url('brand.og_image', setting_url('brand.favicon', asset('images/favicon.png?v=6'))))
@section('rendix.type', 'Service')

@push('rendix.head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service->title,
            'description' => $pageDescription,
            'provider' => ['@type' => 'Organization', 'name' => $siteName, 'url' => url('/')],
            'url' => route('services.show', $service->slug),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

<div>
    <section class="rx-section tight" style="padding-top: 130px;">
        <div class="rx-container">
            <nav class="rx-breadcrumb-public" aria-label="مسیر صفحه">
                <a href="{{ route('home') }}" wire:navigate>خانه</a>
                <i class="fa-solid fa-chevron-left"></i>
                <a href="{{ route('services') }}" wire:navigate>خدمات</a>
                <i class="fa-solid fa-chevron-left"></i>
                <strong>{{ $service->title }}</strong>
            </nav>

            <div class="rx-page-hero" style="padding: 20px 0 40px;">
                <div class="badge fade-up">
                    <span class="badge-dot"></span>
                    خدمت تخصصی {{ $siteName }}
                </div>

                <h1 class="fade-up delay-1">{{ $service->title }}</h1>

                @if ($service->short_description)
                    <p class="lede fade-up delay-2">{{ $service->short_description }}</p>
                @endif

                <div class="rx-hero-actions fade-up delay-3">
                    <a href="{{ route('contact', ['service' => $service->slug]) }}" class="btn btn-primary" wire:navigate>
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>درخواست این خدمت</span>
                    </a>

                    <a href="{{ route('projects') }}" class="btn btn-ghost" wire:navigate>
                        <i class="fa-solid fa-diagram-project"></i>
                        <span>نمونه‌کارها</span>
                    </a>
                </div>
            </div>

            @if ($service->image_path)
                <div class="rx-project-hero rv">
                    <img src="{{ $service->imageUrl() }}" alt="{{ $service->title }}">
                </div>
            @endif
        </div>
    </section>

    @if ($service->description)
        <section class="rx-section tight">
            <div class="rx-container" style="max-width: 900px;">
                <div class="rx-project-block rv">
                    <h2><i class="fa-solid fa-circle-info"></i> درباره این خدمت</h2>
                    <p>{{ $service->description }}</p>
                </div>
            </div>
        </section>
    @endif

    @if ($projects->isNotEmpty())
        <section class="rx-section tight">
            <div class="rx-container">
                <div class="rx-section-head">
                    <h2>نمونه‌کارهای مرتبط</h2>
                    <p>نتیجه این خدمت را در پروژه‌های واقعی ببین.</p>
                </div>

                <div class="rx-portfolio-grid">
                    @foreach ($projects as $project)
                        <article class="rx-project-card rv">
                            <a href="{{ route('projects.show', $project->slug) }}" class="rx-project-media" wire:navigate>
                                @if ($project->image_path)
                                    <img src="{{ $project->imageUrl() }}" alt="{{ $project->title }}" loading="lazy">
                                @else
                                    <div class="rx-project-placeholder"><i class="fa-solid fa-briefcase"></i></div>
                                @endif

                                <span class="rx-project-category">{{ $project->categoryLabel() }}</span>
                            </a>

                            <div class="rx-project-body">
                                <h2>
                                    <a href="{{ route('projects.show', $project->slug) }}" wire:navigate>{{ $project->title }}</a>
                                </h2>
                                <p>{{ \Illuminate\Support\Str::limit($project->short_description ?: $project->categoryLabel(), 90) }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-site.faq location="services" />

    <x-site.cta-band
        title="آماده‌ای این خدمت را فعال کنی؟"
        text="جزئیات را بگو تا پیشنهاد شفاف، زمان‌بندی مشخص و مسیر اجرا را برایت بچینیم."
    />
</div>