<?php

use App\Models\Project;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $category = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $projectId): void
    {
        $project = Project::query()->find($projectId);

        if (! $project) {
            return;
        }

        $project->is_active = ! $project->is_active;
        $project->save();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $project->is_active
                ? 'نمونه‌کار فعال شد.'
                : 'نمونه‌کار غیرفعال شد.',
        ]);
    }

    public function toggleFeatured(int $projectId): void
    {
        $project = Project::query()->find($projectId);

        if (! $project) {
            return;
        }

        $project->is_featured = ! $project->is_featured;
        $project->save();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $project->is_featured
                ? 'نمونه‌کار ویژه شد.'
                : 'وضعیت ویژه برداشته شد.',
        ]);
    }

    public function fetchProjects()
    {
        return Project::query()
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhere('short_description', 'like', "%{$search}%")
                      ->orWhere('client_name', 'like', "%{$search}%");
                });
            })
            ->when($this->status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($this->status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($this->status === 'featured', fn ($query) => $query->where('is_featured', true))
            ->when($this->category !== '', fn ($query) => $query->where('category', $this->category))
            ->ordered()
            ->paginate(12)
            ->withQueryString();
    }
};

?>

@php
    $projects = $this->fetchProjects();
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>نمونه‌کارها</h1>
            <p>پروژه‌های رندیکس را مدیریت کن؛ این‌ها ماشین اعتمادسازی سایت هستند.</p>
        </div>

        <div class="admin-header-actions">
            <a href="{{ route('admin.projects.form') }}" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-plus"></i>
                نمونه‌کار جدید
            </a>
        </div>
    </div>

    <div class="admin-filter-panel">
        <div class="admin-filter-grid">
            <div class="admin-field">
                <label class="admin-label">جستجو</label>
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    class="admin-input"
                    placeholder="عنوان، مشتری، اسلاگ یا توضیح کوتاه..."
                >
            </div>

            <x-admin.choice-pills
                name="status"
                label="وضعیت"
                variant="filter"
                :options="[
                    'active' => 'فعال',
                    'inactive' => 'غیرفعال',
                    'featured' => 'ویژه',
                ]"
                :value="$status"
                placeholder="همه"
            />

            <x-admin.choice-pills
                name="category"
                label="دسته‌بندی"
                variant="filter"
                :options="\App\Models\Project::categoryOptions()"
                :value="$category"
                placeholder="همه"
            />
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-header">
            <h2>فهرست نمونه‌کارها</h2>
            <span class="admin-count">
                {{ Jalali::toPersianDigits(number_format($projects->total())) }} پروژه
            </span>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>پروژه</th>
                        <th>دسته</th>
                        <th>مشتری</th>
                        <th>تحویل</th>
                        <th>وضعیت</th>
                        <th>ویژه</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                        <tr>
                            <td>
                                <div class="admin-table-user">
                                    @if ($project->image_path)
                                        <img
                                            src="{{ $project->imageUrl() }}"
                                            alt="{{ $project->title }}"
                                            class="rx-service-thumb"
                                        >
                                    @else
                                        <span class="admin-avatar small">
                                            {{ mb_substr($project->title, 0, 1) }}
                                        </span>
                                    @endif

                                    <div>
                                        <strong>{{ $project->title }}</strong>
                                        <small dir="ltr">{{ $project->slug }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $project->categoryLabel() }}</td>
                            <td>{{ $project->client_name ?: '—' }}</td>
                            <td>{{ $project->delivered_at ? Jalali::date($project->delivered_at) : '—' }}</td>
                            <td>
                                <button
                                    type="button"
                                    wire:click="toggleActive({{ $project->id }})"
                                    class="admin-badge {{ $project->is_active ? 'status-won' : 'status-archived' }}"
                                >
                                    {{ $project->is_active ? 'فعال' : 'غیرفعال' }}
                                </button>
                            </td>
                            <td>
                                <button
                                    type="button"
                                    wire:click="toggleFeatured({{ $project->id }})"
                                    class="admin-badge {{ $project->is_featured ? 'priority-high' : 'gray' }}"
                                >
                                    {{ $project->is_featured ? 'ویژه' : 'عادی' }}
                                </button>
                            </td>
                            <td>
                                <div class="admin-row-actions">
                                    <a
                                        href="{{ route('projects.show', $project->slug) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="admin-icon-btn"
                                        title="مشاهده در سایت"
                                    >
                                        <i class="fa-solid fa-up-right-from-square"></i>
                                    </a>

                                    <a
                                        href="{{ route('admin.projects.form', ['id' => $project->id]) }}"
                                        class="admin-icon-btn"
                                        title="ویرایش"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="admin-empty">
                                هنوز نمونه‌کاری ثبت نشده است. اولین پروژه را اضافه کن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $projects->links() }}
        </div>
    </div>
</div>