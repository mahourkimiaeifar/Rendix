<?php

use App\Models\Category;
use App\Support\Jalali;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {
    use WithPagination;

    public string $search = '';

    public ?int $selectedId = null;

    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public string $color = '';
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
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->color = '';
        $this->sort_order = '0';
        $this->is_active = true;
    }

    public function newCategory(): void
    {
        $this->resetForm();
    }

    public function editCategory(int $id): void
    {
        $this->selectedId = $id;
        $this->syncFromSelection();
    }

    protected function syncFromSelection(): void
    {
        if (! $this->selectedId) {
            return;
        }

        $category = Category::query()->find($this->selectedId);

        if (! $category) {
            $this->resetForm();

            return;
        }

        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description ?? '';
        $this->color = $category->color ?? '';
        $this->sort_order = (string) $category->sort_order;
        $this->is_active = $category->is_active;
    }

    protected function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($this->slug ?: $name, '-');

        if ($slug === '') {
            $slug = 'category-' . Str::lower(Str::random(6));
        }

        $base = $slug;
        $index = 2;

        while (
            Category::query()
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
        $category = Category::query()->find($id);

        if (! $category) {
            return;
        }

        $category->is_active = ! $category->is_active;
        $category->save();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $category->is_active
                ? 'دسته‌بندی فعال شد.'
                : 'دسته‌بندی غیرفعال شد.',
        ]);
    }

    public function deleteCategory(int $id): void
    {
        $category = Category::query()->find($id);

        if (! $category) {
            return;
        }

        $category->delete();

        if ($this->selectedId === $id) {
            $this->resetForm();
        }

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'دسته‌بندی حذف شد.',
        ]);
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:120', 'alpha_dash'],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [
            'name.required' => 'نام دسته‌بندی را وارد کن.',
            'slug.alpha_dash' => 'اسلاگ فقط می‌تواند حروف انگلیسی، عدد، خط تیره و زیرخط باشد.',
        ]);

        $data = [
            'name' => trim($this->name),
            'slug' => $this->generateSlug(trim($this->name), $this->selectedId),
            'description' => trim($this->description) ?: null,
            'color' => trim($this->color) ?: null,
            'sort_order' => (int) ($this->sort_order ?: 0),
            'is_active' => $this->is_active,
        ];

        if ($this->selectedId) {
            $category = Category::query()->find($this->selectedId);

            if (! $category) {
                $this->dispatch('rx-toast', [
                    'type' => 'error',
                    'message' => 'دسته‌بندی مورد نظر پیدا نشد.',
                ]);

                return;
            }

            $category->update($data);
        } else {
            Category::create($data);
        }

        $this->resetForm();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'دسته‌بندی ذخیره شد.',
        ]);
    }

    public function fetchCategories()
    {
        return Category::query()
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->ordered()
            ->paginate(20)
            ->withQueryString();
    }
};

?>

@php
    $categories = $this->fetchCategories();
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>دسته‌بندی‌های مقالات</h1>
            <p>مقالات را گروه‌بندی کن تا ناوبری و سئو بهتر شود.</p>
        </div>

        <div class="admin-header-actions">
            <button type="button" wire:click="newCategory" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-plus"></i>
                دسته‌بندی جدید
            </button>
        </div>
    </div>

    <div class="rx-manager-grid">
        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>{{ $selectedId ? 'ویرایش دسته‌بندی' : 'افزودن دسته‌بندی' }}</h2>
            </div>

            <form wire:submit="save" class="rx-manager-form">
                <x-admin.form-errors />

                <div class="admin-field">
                    <label class="admin-label">نام دسته‌بندی</label>
                    <input type="text" wire:model.live="name" class="admin-input" required>
                    @error('name') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">اسلاگ</label>
                    <input type="text" wire:model.live="slug" class="admin-input" dir="ltr" placeholder="web-design">
                    <p class="admin-help">اگر خالی بگذاری، از نام ساخته می‌شود.</p>
                    @error('slug') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">توضیح کوتاه</label>
                    <textarea wire:model.live="description" class="admin-input rx-textarea" rows="3"></textarea>
                    @error('description') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="rx-two-col">
                    <div class="admin-field">
                        <label class="admin-label">رنگ</label>
                        <input type="text" wire:model.live="color" class="admin-input" dir="ltr" placeholder="#45C8FF">
                        @error('color') <p class="admin-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="admin-field">
                        <label class="admin-label">ترتیب نمایش</label>
                        <input type="number" wire:model.live="sort_order" class="admin-input" min="0" max="9999">
                        @error('sort_order') <p class="admin-error">{{ $message }}</p> @enderror
                    </div>
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

                    <button type="button" wire:click="newCategory" class="admin-btn admin-btn-ghost">
                        فرم خالی
                    </button>
                </div>
            </form>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>فهرست دسته‌بندی‌ها</h2>
                <span class="admin-count">
                    {{ Jalali::toPersianDigits(number_format($categories->total())) }} دسته
                </span>
            </div>

            <div class="admin-field rx-manager-search">
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    class="admin-input"
                    placeholder="جستجو در نام، اسلاگ یا توضیح..."
                >
            </div>

            <div class="rx-manager-list">
                @forelse ($categories as $category)
                    <div class="rx-manager-item {{ $selectedId === $category->id ? 'is-selected' : '' }}">
                        <div class="rx-manager-item-main">
                            <div>
                                <strong>{{ $category->name }}</strong>
                                <small dir="ltr">{{ $category->slug }}</small>
                                @if ($category->description)
                                    <p>{{ \Illuminate\Support\Str::limit($category->description, 100) }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="rx-manager-actions">
                            <button
                                type="button"
                                wire:click="toggleActive({{ $category->id }})"
                                class="admin-badge {{ $category->is_active ? 'status-won' : 'status-archived' }}"
                            >
                                {{ $category->is_active ? 'فعال' : 'غیرفعال' }}
                            </button>

                            <button type="button" wire:click="editCategory({{ $category->id }})" class="admin-icon-btn" title="ویرایش">
                                <i class="fa-solid fa-pen"></i>
                            </button>

                            <button
                                type="button"
                                wire:click="deleteCategory({{ $category->id }})"
                                wire:confirm="مطمئنی این دسته‌بندی حذف شود؟ مقالات آن بدون دسته می‌مانند."
                                class="admin-icon-btn danger"
                                title="حذف"
                            >
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="admin-empty rx-inline-empty">
                        هنوز دسته‌بندی‌ای ثبت نشده است.
                    </div>
                @endforelse
            </div>

            <div class="admin-pagination">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>