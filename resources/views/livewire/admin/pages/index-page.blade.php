<?php

use App\Models\Page;
use App\Support\Jalali;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {
    use WithPagination;

    public string $search = '';

    public ?int $selectedId = null;

    public string $title = '';
    public string $slug = '';
    public string $content = '';
    public string $meta_title = '';
    public string $meta_description = '';
    public string $sort_order = '0';
    public bool $is_active = true;

    public function mount(): void
    {
        $this->resetForm();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedId(): void
    {
        $this->syncFromSelection();
    }

    public function resetForm(): void
    {
        $this->selectedId = null;
        $this->title = '';
        $this->slug = '';
        $this->content = '';
        $this->meta_title = '';
        $this->meta_description = '';
        $this->sort_order = '0';
        $this->is_active = true;
    }

    public function newPage(): void
    {
        $this->resetForm();
    }

    public function editPage(int $id): void
    {
        $this->selectedId = $id;
        $this->syncFromSelection();
    }

    protected function syncFromSelection(): void
    {
        if (! $this->selectedId) {
            return;
        }

        $page = Page::query()->find($this->selectedId);

        if (! $page) {
            $this->resetForm();

            return;
        }

        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->content = $page->content;
        $this->meta_title = $page->meta_title ?? '';
        $this->meta_description = $page->meta_description ?? '';
        $this->sort_order = (string) $page->sort_order;
        $this->is_active = $page->is_active;
    }

    protected function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($this->slug ?: $title, '-');

        if ($slug === '') {
            $slug = 'page-' . Str::lower(Str::random(6));
        }

        $base = $slug;
        $index = 2;

        while (
            Page::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $index;
            $index++;
        }

        return $slug;
    }

    public function toggleActive(int $id): void
    {
        $page = Page::query()->find($id);

        if (! $page) {
            return;
        }

        $page->is_active = ! $page->is_active;
        $page->save();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $page->is_active
                ? 'صفحه فعال شد.'
                : 'صفحه غیرفعال شد.',
        ]);
    }

    public function deletePage(int $id): void
    {
        $page = Page::query()->find($id);

        if (! $page) {
            return;
        }

        $page->delete();

        if ($this->selectedId === $id) {
            $this->resetForm();
        }

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'صفحه حذف شد.',
        ]);
    }

    public function save()
    {
        $this->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', 'alpha_dash'],
            'content' => ['required', 'string', 'min:10'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [
            'title.required' => 'عنوان صفحه را وارد کن.',
            'content.required' => 'محتوای صفحه را بنویس.',
            'content.min' => 'محتوای صفحه خیلی کوتاه است.',
            'slug.alpha_dash' => 'اسلاگ فقط می‌تواند حروف انگلیسی، عدد، خط تیره و زیرخط باشد.',
        ]);

        $data = [
            'title' => trim($this->title),
            'slug' => $this->generateSlug(trim($this->title), $this->selectedId),
            'content' => trim($this->content),
            'meta_title' => trim($this->meta_title) ?: null,
            'meta_description' => trim($this->meta_description) ?: null,
            'sort_order' => (int) ($this->sort_order ?: 0),
            'is_active' => $this->is_active,
        ];

        if ($this->selectedId) {
            $page = Page::query()->find($this->selectedId);

            if (! $page) {
                $this->dispatch('rx-toast', [
                    'type' => 'error',
                    'message' => 'صفحه مورد نظر پیدا نشد.',
                ]);

                return;
            }

            $page->update($data);
        } else {
            Page::create($data);
        }

        $this->resetForm();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'صفحه ذخیره شد.',
        ]);
    }

    public function fetchPages()
    {
        return Page::query()
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();
    }
};

?>

@php
    $pages = $this->fetchPages();
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>صفحات اختصاصی</h1>
            <p>صفحاتی مثل حریم خصوصی، قوانین، سوالات متداول سفارشی یا هر صفحه محتوایی دیگر بساز.</p>
        </div>

        <div class="admin-header-actions">
            <button type="button" wire:click="newPage" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-plus"></i>
                صفحه جدید
            </button>
        </div>
    </div>

    <div class="rx-manager-grid">
        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>{{ $selectedId ? 'ویرایش صفحه' : 'افزودن صفحه' }}</h2>
            </div>

            <form wire:submit="save" class="rx-manager-form">
                <x-admin.form-errors />

                <div class="admin-field">
                    <label class="admin-label">عنوان صفحه</label>
                    <input type="text" wire:model.live="title" class="admin-input" required>
                    @error('title') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">اسلاگ</label>
                    <input type="text" wire:model.live="slug" class="admin-input" dir="ltr" placeholder="privacy-policy">
                    <p class="admin-help">مثلاً: privacy-policy, terms, refund-policy</p>
                    @error('slug') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">محتوای صفحه</label>
                    <textarea
                        wire:model.live="content"
                        class="admin-input rx-textarea rx-post-editor"
                        rows="16"
                        required
                    ></textarea>
                    @error('content') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="rx-two-col">
                    <div class="admin-field">
                        <label class="admin-label">Meta Title</label>
                        <input type="text" wire:model.live="meta_title" class="admin-input">
                        @error('meta_title') <p class="admin-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="admin-field">
                        <label class="admin-label">ترتیب نمایش</label>
                        <input type="number" wire:model.live="sort_order" class="admin-input" min="0" max="9999">
                        @error('sort_order') <p class="admin-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="admin-field">
                    <label class="admin-label">Meta Description</label>
                    <textarea wire:model.live="meta_description" class="admin-input rx-textarea" rows="3"></textarea>
                    @error('meta_description') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <label class="admin-checkbox rx-switch-card">
                    <input type="checkbox" wire:model.live="is_active">
                    <span>فعال باشد</span>
                </label>

                <div class="rx-edit-actions">
                    <button type="submit" class="admin-btn admin-btn-primary">
                        <span wire:loading.remove wire:target="save">
                            <i class="fa-solid fa-floppy-disk"></i>
                            ذخیره
                        </span>
                        <span wire:loading wire:target="save">
                            در حال ذخیره...
                        </span>
                    </button>

                    <button type="button" wire:click="newPage" class="admin-btn admin-btn-ghost">
                        فرم خالی
                    </button>
                </div>
            </form>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>فهرست صفحات</h2>
                <span class="admin-count">
                    {{ Jalali::toPersianDigits(number_format($pages->total())) }} صفحه
                </span>
            </div>

            <div class="admin-field rx-manager-search">
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    class="admin-input"
                    placeholder="جستجو در عنوان، اسلاگ یا محتوا..."
                >
            </div>

            <div class="rx-manager-list">
                @forelse ($pages as $page)
                    <div class="rx-manager-item {{ $selectedId === $page->id ? 'is-selected' : '' }}">
                        <div class="rx-manager-item-main">
                            <div>
                                <strong>{{ $page->title }}</strong>
                                <small dir="ltr">{{ $page->slug }}</small>
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($page->content), 110) }}</p>
                            </div>
                        </div>

                        <div class="rx-manager-actions">
                            <button
                                type="button"
                                wire:click="toggleActive({{ $page->id }})"
                                class="admin-badge {{ $page->is_active ? 'status-won' : 'status-archived' }}"
                            >
                                {{ $page->is_active ? 'فعال' : 'غیرفعال' }}
                            </button>

                            <a
                                href="{{ route('page', $page->slug) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="admin-icon-btn"
                                title="مشاهده در سایت"
                            >
                                <i class="fa-solid fa-up-right-from-square"></i>
                            </a>

                            <button type="button" wire:click="editPage({{ $page->id }})" class="admin-icon-btn" title="ویرایش">
                                <i class="fa-solid fa-pen"></i>
                            </button>

                            <button
                                type="button"
                                wire:click="deletePage({{ $page->id }})"
                                wire:confirm="مطمئنی این صفحه حذف شود؟"
                                class="admin-icon-btn danger"
                                title="حذف"
                            >
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="admin-empty rx-inline-empty">
                        هنوز صفحه‌ای ثبت نشده است.
                    </div>
                @endforelse
            </div>

            <div class="admin-pagination">
                {{ $pages->links() }}
            </div>
        </div>
    </div>
</div>