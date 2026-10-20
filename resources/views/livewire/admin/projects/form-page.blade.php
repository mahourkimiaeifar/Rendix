<?php

use App\Models\Project;
use App\Support\Jalali;
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
    public ?int $projectId = null;

    public string $title = '';
    public string $slug = '';
    public string $category = 'web-design';

    public string $short_description = '';
    public string $challenge = '';
    public string $solution = '';
    public string $result = '';

    public string $technologies = '';
    public string $demo_url = '';
    public string $client_name = '';

    public string $deliveredAtJalali = '';

    public string $sort_order = '0';

    public bool $is_active = true;
    public bool $is_featured = false;

    public string $image_path = '';
    public $image = null;
    public bool $remove_image = false;

    public array $gallery = [];
    public array $galleryFiles = [];

    public string $meta_title = '';
    public string $meta_description = '';

    public function mount(): void
    {
        $this->syncFromProject();
    }

    public function updatedProjectId(): void
    {
        $this->syncFromProject();
    }

    public function setJalaliToday(string $property): void
    {
        $this->{$property} = Jalali::today();
    }

    public function clearJalali(string $property): void
    {
        $this->{$property} = '';
    }
    
    public function project(): ?Project
    {
        if (!$this->projectId) {
            return null;
        }

        return Project::query()->find($this->projectId);
    }

    protected function syncFromProject(): void
    {
        $project = $this->project();

        if (!$project) {
            return;
        }

        $this->title = $project->title;
        $this->slug = $project->slug;
        $this->category = $project->category;
        $this->short_description = $project->short_description ?? '';
        $this->challenge = $project->challenge ?? '';
        $this->solution = $project->solution ?? '';
        $this->result = $project->result ?? '';
        $this->technologies = $project->technologies ?? '';
        $this->demo_url = $project->demo_url ?? '';
        $this->client_name = $project->client_name ?? '';
        $this->sort_order = (string) $project->sort_order;
        $this->is_active = $project->is_active;
        $this->is_featured = $project->is_featured;
        $this->image_path = $project->image_path ?? '';
        $this->gallery = $project->gallery ?? [];
        $this->meta_title = $project->meta_title ?? '';
        $this->meta_description = $project->meta_description ?? '';

        $this->deliveredAtJalali = Jalali::date($project->delivered_at);
    }

    protected function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($this->slug ?: $title, '-');

        if ($slug === '') {
            $slug = 'project-' . Str::lower(Str::random(6));
        }

        $base = $slug;
        $index = 2;

        while (
            Project::query()
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

        $path = $this->image->store('projects', 'public');

        if ($this->image_path) {
            Storage::disk('public')->delete($this->image_path);
        }

        $this->image_path = $path;
        $this->image = null;
    }

    public function removeGalleryImage(int $index): void
    {
        unset($this->gallery[$index]);

        $this->gallery = array_values($this->gallery);
    }

    public function save()
    {
        $this->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:180', 'alpha_dash'],
            'category' => ['required', 'string', 'in:' . implode(',', array_keys(Project::categoryOptions()))],

            'short_description' => ['nullable', 'string', 'max:255'],
            'challenge' => ['nullable', 'string', 'max:5000'],
            'solution' => ['nullable', 'string', 'max:5000'],
            'result' => ['nullable', 'string', 'max:5000'],

            'technologies' => ['nullable', 'string', 'max:500'],
            'demo_url' => ['nullable', 'url', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:160'],

            'deliveredAtJalali' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (trim((string) $value) !== '' && !Jalali::parse($value)) {
                        $fail('تاریخ تحویل جلالی معتبر نیست.');
                    }
                },
            ],

            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'galleryFiles' => ['nullable', 'array'],
            'galleryFiles.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:300'],
        ], [
            'title.required' => 'عنوان پروژه را وارد کن.',
            'category.in' => 'دسته‌بندی معتبر نیست.',
            'demo_url.url' => 'لینک دمو معتبر نیست.',
            'image.image' => 'تصویر شاخص باید فایل تصویری باشد.',
            'image.mimes' => 'تصویر شاخص فقط JPG, PNG, WEBP مجاز است.',
            'image.max' => 'حجم تصویر شاخص نباید بیشتر از ۲ مگابایت باشد.',
            'galleryFiles.*.image' => 'فایل‌های گالری باید تصویر باشند.',
            'galleryFiles.*.mimes' => 'گالری فقط JPG, PNG, WEBP مجاز است.',
            'galleryFiles.*.max' => 'حجم هر تصویر گالری نباید بیشتر از ۲ مگابایت باشد.',
        ]);

        $this->handleImageUpload();

        foreach ($this->galleryFiles as $file) {
            if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                $this->gallery[] = $file->store('projects/gallery', 'public');
            }
        }

        $this->galleryFiles = [];
        $this->gallery = array_values(array_filter($this->gallery));

        $data = [
            'title' => trim($this->title),
            'slug' => $this->generateSlug(trim($this->title), $this->projectId),
            'category' => $this->category,
            'short_description' => trim($this->short_description) ?: null,
            'challenge' => trim($this->challenge) ?: null,
            'solution' => trim($this->solution) ?: null,
            'result' => trim($this->result) ?: null,
            'technologies' => trim($this->technologies) ?: null,
            'demo_url' => trim($this->demo_url) ?: null,
            'client_name' => trim($this->client_name) ?: null,
            'delivered_at' => Jalali::parse($this->deliveredAtJalali),
            'sort_order' => (int) ($this->sort_order ?: 0),
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'image_path' => $this->image_path ?: null,
            'gallery' => $this->gallery,
            'meta_title' => trim($this->meta_title) ?: null,
            'meta_description' => trim($this->meta_description) ?: null,
        ];

        if ($this->projectId) {
            $project = $this->project();

            if (!$project) {
                $this->dispatch('rx-toast', [
                    'type' => 'error',
                    'message' => 'نمونه‌کار مورد نظر پیدا نشد.',
                ]);

                return redirect()->route('admin.projects.index');
            }

            $project->update($data);
        } else {
            Project::create($data);
        }

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $this->projectId
                ? 'نمونه‌کار با موفقیت به‌روزرسانی شد.'
                : 'نمونه‌کار جدید با موفقیت ثبت شد.',
        ]);

        return redirect()->route('admin.projects.index');
    }
};

?>

@php
    $project = $this->project();
    $isEditing = (bool) $project;
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <a href="{{ route('admin.projects.index') }}" class="rx-back-link">
                <i class="fa-solid fa-arrow-right"></i>
                بازگشت به نمونه‌کارها
            </a>

            <h1>{{ $isEditing ? 'ویرایش نمونه‌کار' : 'نمونه‌کار جدید' }}</h1>
            <p>داستان پروژه را کامل بنویس: چالش، راه‌حل، نتیجه و تکنولوژی‌ها.</p>
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
                    <label class="admin-label">عنوان پروژه</label>
                    <input type="text" wire:model.live="title" class="admin-input" required>
                    @error('title')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">اسلاگ</label>
                    <input type="text" wire:model.live="slug" class="admin-input" dir="ltr"
                        placeholder="online-store-beauty-shop">
                    <p class="admin-help">اگر خالی بگذاری، از عنوان ساخته می‌شود.</p>
                    @error('slug')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <x-admin.choice-pills name="category" label="دسته‌بندی"
                    :options="\App\Models\Project::categoryOptions()" :value="$category" required />

                <div class="admin-field">
                    <label class="admin-label">توضیح کوتاه</label>
                    <textarea wire:model.live="short_description" class="admin-input rx-textarea" rows="2"></textarea>
                    @error('short_description')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">چالش پروژه</label>
                    <textarea wire:model.live="challenge" class="admin-input rx-textarea" rows="4"></textarea>
                    @error('challenge')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">راه‌حل رندیکس</label>
                    <textarea wire:model.live="solution" class="admin-input rx-textarea" rows="4"></textarea>
                    @error('solution')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">نتیجه</label>
                    <textarea wire:model.live="result" class="admin-input rx-textarea" rows="4"></textarea>
                    @error('result')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>جزئیات و رسانه</h2>
            </div>

            <div class="rx-edit-form-inner">
                <div class="admin-field">
                    <label class="admin-label">نام مشتری</label>
                    <input type="text" wire:model.live="client_name" class="admin-input">
                    @error('client_name')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">تکنولوژی‌ها</label>
                    <input type="text" wire:model.live="technologies" class="admin-input" dir="ltr"
                        placeholder="Laravel, Livewire, React, Tailwind">
                    <p class="admin-help">با کاما جدا کن.</p>
                    @error('technologies')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">لینک دمو</label>
                    <input type="url" wire:model.live="demo_url" class="admin-input" dir="ltr">
                    @error('demo_url')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <x-admin.jalali-input name="deliveredAtJalali" label="تاریخ تحویل" :value="$deliveredAtJalali"
                    help="مثال: ۱۴۰۴/۰۷/۲۶" />

                <div class="admin-field">
                    <label class="admin-label">ترتیب نمایش</label>
                    <input type="number" wire:model.live="sort_order" class="admin-input" min="0" max="9999">
                    @error('sort_order')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <label class="admin-checkbox rx-switch-card">
                    <input type="checkbox" wire:model.live="is_active">
                    <span>این نمونه‌کار فعال باشد</span>
                </label>

                <label class="admin-checkbox rx-switch-card">
                    <input type="checkbox" wire:model.live="is_featured">
                    <span>نمونه‌کار ویژه / featured</span>
                </label>

                <div class="admin-field">
                    <label class="admin-label">تصویر شاخص</label>

                    @if ($image_path)
                        <div class="rx-upload-current">
                            <img src="{{ Storage::disk('public')->url($image_path) }}" alt="تصویر شاخص">
                            <label class="admin-checkbox">
                                <input type="checkbox" wire:model.live="remove_image">
                                <span>حذف تصویر شاخص</span>
                            </label>
                        </div>
                    @endif

                    @if ($image)
                        <div class="rx-upload-preview">
                            <img src="{{ $image->temporaryUrl() }}" alt="پیش‌نمایش تصویر شاخص">
                        </div>
                    @endif

                    <input type="file" wire:model="image" class="admin-input" accept="image/*">
                    @error('image')
                    <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">گالری تصاویر</label>

                    @if (!empty($gallery))
                        <div class="rx-gallery-grid">
                            @foreach ($gallery as $path)
                                <div class="rx-gallery-item">
                                    <img src="{{ Storage::disk('public')->url($path) }}" alt="تصویر گالری">

                                    <button type="button" wire:click="removeGalleryImage({{ $loop->index }})"
                                        class="rx-gallery-remove" title="حذف">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if (!empty($galleryFiles))
                        <div class="rx-gallery-grid">
                            @foreach ($galleryFiles as $file)
                                @if ($file instanceof \Illuminate\Http\UploadedFile)
                                    <div class="rx-gallery-item">
                                        <img src="{{ $file->temporaryUrl() }}" alt="پیش‌نمایش گالری">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <input type="file" wire:model="galleryFiles" class="admin-input" accept="image/*" multiple>
                    @error('galleryFiles.*')
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
                            ذخیره نمونه‌کار
                        </span>
                        <span wire:loading wire:target="save">
                            در حال ذخیره...
                        </span>
                    </button>

                    <a href="{{ route('admin.projects.index') }}" class="admin-btn admin-btn-ghost">
                        انصراف
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>