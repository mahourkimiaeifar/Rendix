<?php

use App\Models\Tag;
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
    }

    public function newTag(): void
    {
        $this->resetForm();
    }

    public function editTag(int $id): void
    {
        $this->selectedId = $id;
        $this->syncFromSelection();
    }

    protected function syncFromSelection(): void
    {
        if (! $this->selectedId) {
            return;
        }

        $tag = Tag::query()->find($this->selectedId);

        if (! $tag) {
            $this->resetForm();

            return;
        }

        $this->name = $tag->name;
        $this->slug = $tag->slug;
    }

    protected function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($this->slug ?: $name, '-');

        if ($slug === '') {
            $slug = 'tag-' . Str::lower(Str::random(6));
        }

        $base = $slug;
        $index = 2;

        while (
            Tag::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $index;
            $index++;
        }

        return $slug;
    }

    public function deleteTag(int $id): void
    {
        $tag = Tag::query()->find($id);

        if (! $tag) {
            return;
        }

        $tag->delete();

        if ($this->selectedId === $id) {
            $this->resetForm();
        }

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'تگ حذف شد.',
        ]);
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:120', 'alpha_dash'],
        ], [
            'name.required' => 'نام تگ را وارد کن.',
            'slug.alpha_dash' => 'اسلاگ فقط می‌تواند حروف انگلیسی، عدد، خط تیره و زیرخط باشد.',
        ]);

        $data = [
            'name' => trim($this->name),
            'slug' => $this->generateSlug(trim($this->name), $this->selectedId),
        ];

        if ($this->selectedId) {
            $tag = Tag::query()->find($this->selectedId);

            if (! $tag) {
                $this->dispatch('rx-toast', [
                    'type' => 'error',
                    'message' => 'تگ مورد نظر پیدا نشد.',
                ]);

                return;
            }

            $tag->update($data);
        } else {
            Tag::create($data);
        }

        $this->resetForm();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'تگ ذخیره شد.',
        ]);
    }

    public function fetchTags()
    {
        return Tag::query()
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();
    }
};

?>

@php
    $tags = $this->fetchTags();
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>تگ‌های مقالات</h1>
            <p>تگ‌ها به کشف مقالات مرتبط و ناوبری داخلی کمک می‌کنند.</p>
        </div>

        <div class="admin-header-actions">
            <button type="button" wire:click="newTag" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-plus"></i>
                تگ جدید
            </button>
        </div>
    </div>

    <div class="rx-manager-grid">
        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>{{ $selectedId ? 'ویرایش تگ' : 'افزودن تگ' }}</h2>
            </div>

            <form wire:submit="save" class="rx-manager-form">
                <x-admin.form-errors />

                <div class="admin-field">
                    <label class="admin-label">نام تگ</label>
                    <input type="text" wire:model.live="name" class="admin-input" required>
                    @error('name') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">اسلاگ</label>
                    <input type="text" wire:model.live="slug" class="admin-input" dir="ltr" placeholder="seo">
                    <p class="admin-help">اگر خالی بگذاری، از نام ساخته می‌شود.</p>
                    @error('slug') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

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

                    <button type="button" wire:click="newTag" class="admin-btn admin-btn-ghost">
                        فرم خالی
                    </button>
                </div>
            </form>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>فهرست تگ‌ها</h2>
                <span class="admin-count">
                    {{ Jalali::toPersianDigits(number_format($tags->total())) }} تگ
                </span>
            </div>

            <div class="admin-field rx-manager-search">
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    class="admin-input"
                    placeholder="جستجو در نام یا اسلاگ..."
                >
            </div>

            <div class="rx-manager-list">
                @forelse ($tags as $tag)
                    <div class="rx-manager-item {{ $selectedId === $tag->id ? 'is-selected' : '' }}">
                        <div class="rx-manager-item-main">
                            <div>
                                <strong>{{ $tag->name }}</strong>
                                <small dir="ltr">{{ $tag->slug }}</small>
                            </div>
                        </div>

                        <div class="rx-manager-actions">
                            <button type="button" wire:click="editTag({{ $tag->id }})" class="admin-icon-btn" title="ویرایش">
                                <i class="fa-solid fa-pen"></i>
                            </button>

                            <button
                                type="button"
                                wire:click="deleteTag({{ $tag->id }})"
                                wire:confirm="مطمئنی این تگ حذف شود؟"
                                class="admin-icon-btn danger"
                                title="حذف"
                            >
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="admin-empty rx-inline-empty">
                        هنوز تگی ثبت نشده است.
                    </div>
                @endforelse
            </div>

            <div class="admin-pagination">
                {{ $tags->links() }}
            </div>
        </div>
    </div>
</div>