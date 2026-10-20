<?php

use App\Models\Project;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function project(): ?Project
    {
        return Project::query()
            ->active()
            ->where('slug', $this->slug)
            ->first();
    }
};

?>

@php
    $project = $this->project();

    if (! $project) {
        abort(404);
    }
@endphp

<section class="rx-project-detail">
    <div class="rx-container">
        <a href="{{ route('projects') }}" class="rx-back-link public">
            <i class="fa-solid fa-arrow-right"></i>
            بازگشت به نمونه‌کارها
        </a>

        <div class="rx-project-detail-head">
            <span class="rx-project-category large">
                {{ $project->categoryLabel() }}
            </span>

            <h1>{{ $project->title }}</h1>

            @if ($project->short_description)
                <p class="rx-project-lede">{{ $project->short_description }}</p>
            @endif
        </div>

        @if ($project->image_path)
            <div class="rx-project-hero">
                <img src="{{ $project->imageUrl() }}" alt="{{ $project->title }}">
            </div>
        @endif

        <div class="rx-project-detail-grid">
            <div class="rx-project-main">
                @if ($project->challenge)
                    <div class="rx-project-block">
                        <h2><i class="fa-solid fa-mountain"></i> چالش</h2>
                        <p>{{ $project->challenge }}</p>
                    </div>
                @endif

                @if ($project->solution)
                    <div class="rx-project-block">
                        <h2><i class="fa-solid fa-wand-magic-sparkles"></i> راه‌حل رندیکس</h2>
                        <p>{{ $project->solution }}</p>
                    </div>
                @endif

                @if ($project->result)
                    <div class="rx-project-block">
                        <h2><i class="fa-solid fa-chart-line"></i> نتیجه</h2>
                        <p>{{ $project->result }}</p>
                    </div>
                @endif

                @if ($project->galleryUrls())
                    <div class="rx-project-block">
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
                <div class="rx-project-info-card">
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
                                <div class="rx-tech-tags vertical">
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

                    <a href="{{ route('contact', ['service' => $project->category]) }}" class="rx-project-cta-btn">
                        <i class="fa-solid fa-rocket"></i>
                        مشابه این پروژه را می‌خواهم
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>