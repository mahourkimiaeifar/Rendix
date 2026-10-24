<?php

use App\Models\Post;
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

    public function togglePublish(int $postId): void
    {
        $post = Post::query()->find($postId);

        if (! $post) {
            return;
        }

        if ($post->status === 'published') {
            $post->status = 'draft';
        } else {
            $post->status = 'published';

            if (! $post->published_at) {
                $post->published_at = now();
            }
        }

        $post->save();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $post->status === 'published'
                ? 'مقاله منتشر شد.'
                : 'مقاله به پیش‌نویس برگشت.',
        ]);
    }

    public function toggleFeatured(int $postId): void
    {
        $post = Post::query()->find($postId);

        if (! $post) {
            return;
        }

        $post->is_featured = ! $post->is_featured;
        $post->save();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $post->is_featured
                ? 'مقاله ویژه شد.'
                : 'وضعیت ویژه برداشته شد.',
        ]);
    }

    public function deletePost(int $postId): void
    {
        $post = Post::query()->find($postId);

        if (! $post) {
            return;
        }

        if ($post->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'مقاله حذف شد.',
        ]);
    }

    public function fetchPosts()
    {
        return Post::query()
            ->with(['category', 'author'])
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhere('excerpt', 'like', "%{$search}%");
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when($this->category !== '', fn ($query) => $query->where('category_id', $this->category))
            ->ordered()
            ->paginate(15)
            ->withQueryString();
    }
};

?>

@php
    $posts = $this->fetchPosts();

    try {
        $categoryOptions = \App\Models\Category::activeOptions();
    } catch (\Throwable $e) {
        $categoryOptions = [];
    }
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>مقالات</h1>
            <p>محتوای سئویی رندیکس را بنویس، منتشر کن و مدیریت کن.</p>
        </div>

        <div class="admin-header-actions">
            <a href="{{ route('admin.posts.form') }}" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-plus"></i>
                مقاله جدید
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
                    placeholder="عنوان، اسلاگ یا خلاصه..."
                >
            </div>

            <x-admin.choice-pills
                name="status"
                label="وضعیت"
                variant="filter"
                :options="\App\Models\Post::statusOptions()"
                :value="$status"
                placeholder="همه"
            />

            @if (! empty($categoryOptions))
                <div class="admin-field">
                    <label class="admin-label">دسته‌بندی</label>
                    <select wire:model.live="category" class="admin-input">
                        <option value="">همه دسته‌ها</option>
                        @foreach ($categoryOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-header">
            <h2>فهرست مقالات</h2>
            <span class="admin-count">
                {{ Jalali::toPersianDigits(number_format($posts->total())) }} مقاله
            </span>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>مقاله</th>
                        <th>دسته</th>
                        <th>نویسنده</th>
                        <th>وضعیت</th>
                        <th>انتشار</th>
                        <th>بازدید</th>
                        <th>ویژه</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr>
                            <td>
                                <div class="admin-table-user">
                                    @if ($post->image_path)
                                        <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="rx-service-thumb">
                                    @else
                                        <span class="admin-avatar small">{{ mb_substr($post->title, 0, 1) }}</span>
                                    @endif

                                    <div>
                                        <strong>{{ $post->title }}</strong>
                                        <small dir="ltr">{{ $post->slug }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $post->category?->name ?: '—' }}</td>
                            <td>{{ $post->author?->name ?: '—' }}</td>
                            <td>
                                <button
                                    type="button"
                                    wire:click="togglePublish({{ $post->id }})"
                                    class="admin-badge {{ $post->status === 'published' ? 'status-won' : ($post->status === 'archived' ? 'status-archived' : 'priority-normal') }}"
                                >
                                    {{ $post->statusLabel() }}
                                </button>
                            </td>
                            <td>{{ $post->published_at ? Jalali::date($post->published_at) : '—' }}</td>
                            <td>{{ Jalali::toPersianDigits(number_format($post->views)) }}</td>
                            <td>
                                <button
                                    type="button"
                                    wire:click="toggleFeatured({{ $post->id }})"
                                    class="admin-badge {{ $post->is_featured ? 'priority-high' : 'gray' }}"
                                >
                                    {{ $post->is_featured ? 'ویژه' : 'عادی' }}
                                </button>
                            </td>
                            <td>
                                <div class="admin-row-actions">
                                    @if ($post->status === 'published')
                                        <a
                                            href="{{ route('blog.show', $post->slug) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="admin-icon-btn"
                                            title="مشاهده در سایت"
                                        >
                                            <i class="fa-solid fa-up-right-from-square"></i>
                                        </a>
                                    @endif

                                    <a
                                        href="{{ route('admin.posts.form', ['id' => $post->id]) }}"
                                        class="admin-icon-btn"
                                        title="ویرایش"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <button
                                        type="button"
                                        wire:click="deletePost({{ $post->id }})"
                                        wire:confirm="مطمئنی این مقاله حذف شود؟ این عمل قابل بازگشت نیست."
                                        class="admin-icon-btn danger"
                                        title="حذف"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="admin-empty">
                                هنوز مقاله‌ای ثبت نشده است. اولین مقاله را بنویس.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $posts->links() }}
        </div>
    </div>
</div>