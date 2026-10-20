<?php

use App\Models\Testimonial;
use App\Support\Jalali;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public ?int $selectedId = null;

    public string $name = '';
    public string $role = '';
    public string $company = '';
    public string $content = '';
    public string $rating = '5';
    public string $sort_order = '0';
    public bool $is_active = true;

    public string $avatar_path = '';
    public $avatar = null;
    public bool $remove_avatar = false;

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
        $this->role = '';
        $this->company = '';
        $this->content = '';
        $this->rating = '5';
        $this->sort_order = '0';
        $this->is_active = true;
        $this->avatar_path = '';
        $this->avatar = null;
        $this->remove_avatar = false;
    }

    public function newTestimonial(): void
    {
        $this->resetForm();
    }

    public function editTestimonial(int $id): void
    {
        $this->selectedId = $id;
        $this->syncFromSelection();
    }

    protected function syncFromSelection(): void
    {
        if (! $this->selectedId) {
            return;
        }

        $testimonial = Testimonial::query()->find($this->selectedId);

        if (! $testimonial) {
            $this->resetForm();

            return;
        }

        $this->name = $testimonial->name;
        $this->role = $testimonial->role ?? '';
        $this->company = $testimonial->company ?? '';
        $this->content = $testimonial->content;
        $this->rating = (string) $testimonial->rating;
        $this->sort_order = (string) $testimonial->sort_order;
        $this->is_active = $testimonial->is_active;
        $this->avatar_path = $testimonial->avatar_path ?? '';
    }

    public function deleteTestimonial(int $id): void
    {
        $testimonial = Testimonial::query()->find($id);

        if (! $testimonial) {
            return;
        }

        if ($testimonial->avatar_path) {
            Storage::disk('public')->delete($testimonial->avatar_path);
        }

        $testimonial->delete();

        if ($this->selectedId === $id) {
            $this->resetForm();
        }

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'نظر مشتری حذف شد.',
        ]);
    }

    public function toggleActive(int $id): void
    {
        $testimonial = Testimonial::query()->find($id);

        if (! $testimonial) {
            return;
        }

        $testimonial->is_active = ! $testimonial->is_active;
        $testimonial->save();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $testimonial->is_active
                ? 'نظر فعال شد.'
                : 'نظر غیرفعال شد.',
        ]);
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:120'],
            'content' => ['required', 'string', 'max:1000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ], [
            'name.required' => 'نام مشتری را وارد کن.',
            'content.required' => 'متن نظر را بنویس.',
            'rating.between' => 'امتیاز باید بین ۱ تا ۵ باشد.',
            'avatar.image' => 'آواتار باید تصویر باشد.',
            'avatar.max' => 'حجم آواتار نباید بیشتر از ۱ مگابایت باشد.',
        ]);

        if ($this->remove_avatar) {
            if ($this->avatar_path) {
                Storage::disk('public')->delete($this->avatar_path);
            }

            $this->avatar_path = '';
            $this->remove_avatar = false;
        }

        if ($this->avatar !== null) {
            $path = $this->avatar->store('testimonials', 'public');

            if ($this->avatar_path) {
                Storage::disk('public')->delete($this->avatar_path);
            }

            $this->avatar_path = $path;
            $this->avatar = null;
        }

        $data = [
            'name' => trim($this->name),
            'role' => trim($this->role) ?: null,
            'company' => trim($this->company) ?: null,
            'content' => trim($this->content),
            'rating' => (int) $this->rating,
            'sort_order' => (int) ($this->sort_order ?: 0),
            'is_active' => $this->is_active,
            'avatar_path' => $this->avatar_path ?: null,
        ];

        if ($this->selectedId) {
            $testimonial = Testimonial::query()->find($this->selectedId);

            if (! $testimonial) {
                $this->dispatch('rx-toast', [
                    'type' => 'error',
                    'message' => 'نظر مورد نظر پیدا نشد.',
                ]);

                return;
            }

            $testimonial->update($data);
        } else {
            Testimonial::create($data);
        }

        $this->resetForm();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'نظر مشتری ذخیره شد.',
        ]);
    }

    public function fetchTestimonials()
    {
        return Testimonial::query()
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->ordered()
            ->paginate(10)
            ->withQueryString();
    }
};

?>

@php
    $testimonials = $this->fetchTestimonials();
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>نظرات مشتریان</h1>
            <p>بازخوردهای واقعی را مدیریت کن تا اعتماد سایت بالا برود.</p>
        </div>

        <div class="admin-header-actions">
            <button type="button" wire:click="newTestimonial" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-plus"></i>
                نظر جدید
            </button>
        </div>
    </div>

    <div class="rx-manager-grid">
        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>{{ $selectedId ? 'ویرایش نظر' : 'افزودن نظر' }}</h2>
            </div>

            <form wire:submit="save" class="rx-manager-form">
                <x-admin.form-errors />

                <div class="admin-field">
                    <label class="admin-label">نام مشتری</label>
                    <input type="text" wire:model.live="name" class="admin-input" required>
                    @error('name') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="rx-two-col">
                    <div class="admin-field">
                        <label class="admin-label">سمت</label>
                        <input type="text" wire:model.live="role" class="admin-input">
                        @error('role') <p class="admin-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="admin-field">
                        <label class="admin-label">شرکت</label>
                        <input type="text" wire:model.live="company" class="admin-input">
                        @error('company') <p class="admin-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="admin-field">
                    <label class="admin-label">متن نظر</label>
                    <textarea wire:model.live="content" class="admin-input rx-textarea" rows="5" required></textarea>
                    @error('content') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <x-admin.choice-pills
                    name="rating"
                    label="امتیاز"
                    :options="[
                        '1' => '۱',
                        '2' => '۲',
                        '3' => '۳',
                        '4' => '۴',
                        '5' => '۵',
                    ]"
                    :value="$rating"
                />

                <div class="rx-two-col">
                    <div class="admin-field">
                        <label class="admin-label">ترتیب نمایش</label>
                        <input type="number" wire:model.live="sort_order" class="admin-input" min="0" max="9999">
                        @error('sort_order') <p class="admin-error">{{ $message }}</p> @enderror
                    </div>

                    <label class="admin-checkbox rx-switch-card">
                        <input type="checkbox" wire:model.live="is_active">
                        <span>فعال باشد</span>
                    </label>
                </div>

                <div class="admin-field">
                    <label class="admin-label">آواتار</label>

                    @if ($avatar_path)
                        <div class="rx-upload-current rx-avatar-current">
                            <img src="{{ Storage::disk('public')->url($avatar_path) }}" alt="آواتار">
                            <label class="admin-checkbox">
                                <input type="checkbox" wire:model.live="remove_avatar">
                                <span>حذف</span>
                            </label>
                        </div>
                    @endif

                    @if ($avatar)
                        <div class="rx-upload-preview rx-avatar-preview">
                            <img src="{{ $avatar->temporaryUrl() }}" alt="پیش‌نمایش آواتار">
                        </div>
                    @endif

                    <input type="file" wire:model="avatar" class="admin-input" accept="image/*">
                    @error('avatar') <p class="admin-error">{{ $message }}</p> @enderror
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

                    <button type="button" wire:click="newTestimonial" class="admin-btn admin-btn-ghost">
                        فرم خالی
                    </button>
                </div>
            </form>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>فهرست نظرات</h2>
                <span class="admin-count">
                    {{ Jalali::toPersianDigits(number_format($testimonials->total())) }} نظر
                </span>
            </div>

            <div class="admin-field rx-manager-search">
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    class="admin-input"
                    placeholder="جستجو در نام، شرکت یا متن نظر..."
                >
            </div>

            <div class="rx-manager-list">
                @forelse ($testimonials as $testimonial)
                    <div class="rx-manager-item {{ $selectedId === $testimonial->id ? 'is-selected' : '' }}">
                        <div class="rx-manager-item-main">
                            @if ($testimonial->avatar_path)
                                <img src="{{ $testimonial->avatarUrl() }}" alt="{{ $testimonial->name }}" class="rx-manager-avatar">
                            @else
                                <span class="rx-manager-avatar-placeholder">{{ $testimonial->initials() }}</span>
                            @endif

                            <div>
                                <strong>{{ $testimonial->name }}</strong>
                                <small>
                                    {{ $testimonial->company ?: $testimonial->role ?: '—' }}
                                    —
                                    {{ str_repeat('★', $testimonial->rating) }}{{ str_repeat('☆', 5 - $testimonial->rating) }}
                                </small>
                                <p>{{ \Illuminate\Support\Str::limit($testimonial->content, 110) }}</p>
                            </div>
                        </div>

                        <div class="rx-manager-actions">
                            <button
                                type="button"
                                wire:click="toggleActive({{ $testimonial->id }})"
                                class="admin-badge {{ $testimonial->is_active ? 'status-won' : 'status-archived' }}"
                            >
                                {{ $testimonial->is_active ? 'فعال' : 'غیرفعال' }}
                            </button>

                            <button type="button" wire:click="editTestimonial({{ $testimonial->id }})" class="admin-icon-btn" title="ویرایش">
                                <i class="fa-solid fa-pen"></i>
                            </button>

                            <button
                                type="button"
                                wire:click="deleteTestimonial({{ $testimonial->id }})"
                                wire:confirm="مطمئنی این نظر حذف شود؟"
                                class="admin-icon-btn danger"
                                title="حذف"
                            >
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="admin-empty rx-inline-empty">
                        هنوز نظری ثبت نشده است.
                    </div>
                @endforelse
            </div>

            <div class="admin-pagination">
                {{ $testimonials->links() }}
            </div>
        </div>
    </div>
</div>