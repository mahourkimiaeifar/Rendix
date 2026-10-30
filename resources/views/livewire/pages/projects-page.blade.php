<?php

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    #[Url]
    public string $category = '';

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function fetchProjects()
    {
        try {
            return Project::query()
                ->active()
                ->when($this->category !== '', fn ($query) => $query->where('category', $this->category))
                ->ordered()
                ->paginate(9)
                ->withQueryString();
        } catch (\Throwable $e) {
            return collect();
        }
    }
};

?>

@php
    $projects = $this->fetchProjects();
    $categories = ['' => 'همه'] + \App\Models\Project::categoryOptions();
    $hasPagination = is_object($projects) && method_exists($projects, 'links');
    $siteName = setting('site.name', 'رندیکس');
@endphp

@section('rendix.title', 'نمونه‌کارها | ' . $siteName)
@section('rendix.description', 'پروژه‌های واقعی رندیکس؛ طراحی وبسایت، فروشگاه اینترنتی، سئو و کمپین‌هایی که نتیجه داده‌اند.')
@section('rendix.canonical', route('projects'))
@section('rendix.type', 'WebPage')

<div>
    <x-site.page-hero
        badge="نمونه‌کارهای رندیکس"
        title="پروژه‌هایی که"
        gradient="با فکر و نتیجه ساخته شده‌اند."
        lede="هر پروژه یک مسیر است؛ از شناخت کسب‌وکار تا طراحی، اجرا و رشد. اینجا بخشی از این مسیرها را می‌بینی."
    />

    <section class="rx-section tight" style="padding-top: 0;">
        <div class="rx-container">
            <div class="rx-filter-row">
                @foreach ($categories as $value => $label)
                    <a
                        href="{{ route('projects', ['category' => $value]) }}"
                        class="rx-filter-pill {{ $category === $value ? 'is-active' : '' }}"
                        wire:navigate
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @if (! $hasPagination || $projects->count() === 0)
                <x-site.empty-state
                    icon="fa-solid fa-diagram-project"
                    title="هنوز نمونه‌کاری در این دسته نیست"
                    text="به‌زودی پروژه‌های بیشتری منتشر می‌شود؛ فعلاً می‌توانی همه دسته‌ها را ببینی یا پروژه‌ات را شروع کنی."
                >
                    <div class="flex flex-wrap justify-center gap-3 mt-4">
                        <a href="{{ route('projects') }}" class="btn btn-ghost btn-sm" wire:navigate>
                            <i class="fa-solid fa-rotate"></i>
                            <span>نمایش همه</span>
                        </a>

                        <a href="{{ route('request-project') }}" class="btn btn-primary btn-sm" wire:navigate>
                            <i class="fa-solid fa-rocket"></i>
                            <span>درخواست پروژه</span>
                        </a>
                    </div>
                </x-site.empty-state>
            @else
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

                                @if ($project->short_description)
                                    <p>{{ $project->short_description }}</p>
                                @endif

                                @if ($project->technologyList())
                                    <div class="rx-tech-tags">
                                        @foreach (array_slice($project->technologyList(), 0, 4) as $tech)
                                            <span>{{ $tech }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <a href="{{ route('projects.show', $project->slug) }}" class="rx-project-link" wire:navigate>
                                    مشاهده پروژه
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($hasPagination)
                    <div class="rx-public-pagination">
                        {{ $projects->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>

    <x-site.cta-band
        title="پروژه بعدی این فهرست، مال تو است"
        text="ایده‌ات را بگو؛ ما آن را به یک نمونه‌کار قابل افتخار تبدیل می‌کنیم."
    />
</div>