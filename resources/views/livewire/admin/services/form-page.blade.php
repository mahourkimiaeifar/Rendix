<?php

use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')]
    class extends Component {
    use WithFileUploads;

    #[Url(as: 'id')]
    public ?int $serviceId = null;

    public string $title = '';
    public string $slug = '';
    public string $icon = '';

    public string $short_description = '';
    public string $description = '';

    public string $metric_value = '';
    public string $metric_label = '';

    public string $sort_order = '0';

    public bool $is_active = true;
    public bool $is_featured = false;

    public string $image_path = '';
    public $image = null;
    public bool $remove_image = false;

    public string $meta_title = '';
    public string $meta_description = '';

    public function mount(): void
    {
        $this->syncFromService();
    }

    public function updatedServiceId(): void
    {
        $this->syncFromService();
    }

    public function service(): ?Service
    {
        if (!$this->serviceId) {
            return null;
        }

        return Service::query()->find($this->serviceId);
    }

    protected function syncFromService(): void
    {
        $service = $this->service();

        if (!$service) {
            return;
        }

        $this->title = $service->title;
        $this->slug = $service->slug;
        $this->icon = $service->icon ?? '';
        $this->short_description = $service->short_description ?? '';
        $this->description = $service->description ?? '';
        $this->metric_value = $service->metric_value ?? '';
        $this->metric_label = $service->metric_label ?? '';
        $this->sort_order = (string) $service->sort_order;
        $this->is_active = $service->is_active;
        $this->is_featured = $service->is_featured;
        $this->image_path = $service->image_path ?? '';
        $this->meta_title = $service->meta_title ?? '';
        $this->meta_description = $service->meta_description ?? '';
    }

    protected function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($this->slug ?: $title, '-');

        if ($slug === '') {
            $slug = 'service-' . Str::lower(Str::random(6));
        }

        $base = $slug;
        $index = 2;

        while (
            Service::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $index;
            $index++;
        }

        return $slug;
    }

    protected function handleImageUpload(): void
    {
        if ($this->remove_image) {
            if ($this->image_path) {
                Storage::disk('public')->delete($this->image_path);
            }

            $this->image_path = '';
            $this->remove_image = false;

            return;
        }

        if ($this->image === null) {
            return;
        }

        $path = $this->image->store('services', 'public');

        if ($this->image_path) {
            Storage::disk('public')->delete($this->image_path);
        }

        $this->image_path = $path;
        $this->image = null;
    }

    public function save()
    {
        $this->validate([
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:160', 'alpha_dash'],
            'icon' => ['nullable', 'string', 'max:120'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'metric_value' => ['nullable', 'string', 'max:40'],
            'metric_label' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'meta_title' => ['nullable', 'string', 'max:160'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'title.required' => 'عنوان خدمت را وارد کن.',
            'slug.alpha_dash' => 'اسلاگ فقط می‌تواند حروف انگلیسی، عدد، خط تیره و زیرخط باشد.',
            'short_description.max' => 'توضیح کوتاه نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'image.image' => 'فایل انتخابی باید تصویر باشد.',
            'image.mimes' => 'تصویر فقط JPG, PNG, WEBP مجاز است.',
            'image.max' => 'حجم تصویر نباید بیشتر از ۲ مگابایت باشد.',
        ]);

        $this->handleImageUpload();

        $data = [
            'title' => trim($this->title),
            'slug' => $this->generateSlug(trim($this->title), $this->serviceId),
            'icon' => trim($this->icon) ?: null,
            'short_description' => trim($this->short_description) ?: null,
            'description' => trim($this->description) ?: null,
            'metric_value' => trim($this->metric_value) ?: null,
            'metric_label' => trim($this->metric_label) ?: null,
            'sort_order' => (int) ($this->sort_order ?: 0),
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'image_path' => $this->image_path ?: null,
            'meta_title' => trim($this->meta_title) ?: null,
            'meta_description' => trim($this->meta_description) ?: null,
        ];

        if ($this->serviceId) {
            $service = $this->service();

            if (!$service) {
                $this->dispatch('rx-toast', [
                    'type' => 'error',
                    'message' => 'خدمت مورد نظر پیدا نشد.',
                ]);

                return redirect()->route('admin.services.index');
            }

            $service->update($data);
        } else {
            Service::create($data);
        }

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $this->serviceId
                ? 'خدمت با موفقیت به‌روزرسانی شد.'
                : 'خدمت جدید با موفقیت ثبت شد.',
        ]);

        return redirect()->route('admin.services.index');
    }
};

?>

@php
    $service = $this->service();
    $isEditing = (bool) $service;
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <a href="{{ route('admin.services.index') }}" class="rx-back-link">
                <i class="fa-solid fa-arrow-right"></i>
                بازگشت به خدمات
            </a>

            <h1>{{ $isEditing ? 'ویرایش خدمت' : 'خدمت جدید' }}</h1>
            <p>عنوان، توضیحات، تصویر، ترتیب نمایش و سئوی خدمت را تنظیم کن.</p>
        </div>
    </div>

    <form wire:submit="save" class="rx-edit-form-grid">
        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>اطلاعات اصلی</h2>
            </div>

            <div class="rx-edit-form-inner">
                <x-admin.form-errors />

                <div class="admin-field">
                    <label class="admin-label">عنوان خدمت</label>
                    <input type="text" wire:model.live="title" class="admin-input" required>
                    @error('title')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">اسلاگ</label>
                    <input type="text" wire:model.live="slug" class="admin-input" dir="ltr" placeholder="web-design">
                    <p class="admin-help">اگر خالی بگذاری، از عنوان ساخته می‌شود.</p>
                    @error('slug')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">آیکون Font Awesome</label>
                    <input type="text" wire:model.live="icon" class="admin-input" dir="ltr"
                        placeholder="fa-solid fa-code">
                    @error('icon')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">توضیح کوتاه</label>
                    <textarea wire:model.live="short_description" class="admin-input rx-textarea" rows="2"></textarea>
                    @error('short_description')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">توضیح کامل</label>
                    <textarea wire:model.live="description" class="admin-input rx-textarea" rows="8"></textarea>
                    @error('description')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>نمایش و سئو</h2>
            </div>

            <div class="rx-edit-form-inner">
                <div class="rx-settings-grid-compact">
                    <div class="admin-field">
                        <label class="admin-label">متریک مقدار</label>
                        <input type="text" wire:model.live="metric_value" class="admin-input" placeholder="۹۸٪">
                        @error('metric_value')
                        <p class="admin-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="admin-field">
                        <label class="admin-label">متریک برچسب</label>
                        <input type="text" wire:model.live="metric_label" class="admin-input" placeholder="رضایت مشتری">
                        @error('metric_label')
                        <p class="admin-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="admin-field">
                    <label class="admin-label">ترتیب نمایش</label>
                    <input type="number" wire:model.live="sort_order" class="admin-input" min="0" max="9999">
                    @error('sort_order')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <label class="admin-checkbox rx-switch-card">
                    <input type="checkbox" wire:model.live="is_active">
                    <span>این خدمت فعال باشد</span>
                </label>

                <label class="admin-checkbox rx-switch-card">
                    <input type="checkbox" wire:model.live="is_featured">
                    <span>خدمت ویژه / featured</span>
                </label>

                <div class="admin-field">
                    <label class="admin-label">تصویر خدمت</label>

                    @if ($image_path)
                        <div class="rx-upload-current">
                            <img src="{{ Storage::disk('public')->url($image_path) }}" alt="تصویر خدمت">
                            <label class="admin-checkbox">
                                <input type="checkbox" wire:model.live="remove_image">
                                <span>حذف تصویر</span>
                            </label>
                        </div>
                    @endif

                    @if ($image)
                        <div class="rx-upload-preview">
                            <img src="{{ $image->temporaryUrl() }}" alt="پیش‌نمایش تصویر">
                        </div>
                    @endif

                    <input type="file" wire:model="image" class="admin-input" accept="image/*">
                    @error('image')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">Meta Title</label>
                    <input type="text" wire:model.live="meta_title" class="admin-input">
                    @error('meta_title')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">Meta Description</label>
                    <textarea wire:model.live="meta_description" class="admin-input rx-textarea" rows="3"></textarea>
                    @error('meta_description')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="rx-edit-actions">
                    <button type="submit" class="admin-btn admin-btn-primary">
                        <span wire:loading.remove wire:target="save">
                            <i class="fa-solid fa-floppy-disk"></i>
                            ذخیره خدمت
                        </span>
                        <span wire:loading wire:target="save">
                            در حال ذخیره...
                        </span>
                    </button>

                    <a href="{{ route('admin.services.index') }}" class="admin-btn admin-btn-ghost">
                        انصراف
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>