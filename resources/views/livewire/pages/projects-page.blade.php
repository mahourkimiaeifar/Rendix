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
        return Project::query()
            ->active()
            ->when($this->category !== '', fn ($query) => $query->where('category', $this->category))
            ->ordered()
            ->paginate(9)
            ->withQueryString();
    }
};

?>

@php
    $projects = $this->fetchProjects();
    $categories = ['' => 'همه'] + \App\Models\Project::categoryOptions();
@endphp

<section class="rx-portfolio-section">
    <div class="rx-container">
        <div class="rx-section-head">
            <h1>نمونه‌کارهای رندیکس</h1>
            <p>پروژه‌هایی که با فکر، سرعت و نتیجه ساخته شده‌اند.</p>
        </div>

        <div class="rx-filter-row">
            @foreach ($categories as $value => $label)
                <a
                    href="{{ route('projects', ['category' => $value]) }}"
                    class="rx-filter-pill {{ $category === $value ? 'is-active' : '' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if ($projects->isEmpty())
            <div class="rx-empty-state">
                <i class="fa-solid fa-satellite-dish"></i>
                <h2>هنوز نمونه‌کاری در این دسته ثبت نشده است</h2>
                <p>به‌زودی پروژه‌های بیشتری اینجا منتشر می‌شود.</p>
            </div>
        @else
            <div class="rx-portfolio-grid">
                @foreach ($projects as $project)
                    <article class="rx-project-card">
                        <a href="{{ route('projects.show', $project->slug) }}" class="rx-project-media">
                            @if ($project->image_path)
                                <img src="{{ $project->imageUrl() }}" alt="{{ $project->title }}" loading="lazy">
                            @else
                                <div class="rx-project-placeholder">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                            @endif

                            <span class="rx-project-category">
                                {{ $project->categoryLabel() }}
                            </span>
                        </a>

                        <div class="rx-project-body">
                            <h2>
                                <a href="{{ route('projects.show', $project->slug) }}">
                                    {{ $project->title }}
                                </a>
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

                            <a href="{{ route('projects.show', $project->slug) }}" class="rx-project-link">
                                مشاهده پروژه
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="rx-public-pagination">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</section>