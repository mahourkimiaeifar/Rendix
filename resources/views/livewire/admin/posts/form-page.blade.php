<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Support\Jalali;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {
    use WithFileUploads;

    #[Url(as: 'id')]
    public ?int $postId = null;

    public string $title = '';
    public string $slug = '';
    public string $excerpt = '';
    public string $content = '';

    public string $categoryId = '';
    public string $tagsInput = '';

    public string $status = 'draft';
    public string $publishedAtJalali = '';

    public string $meta_title = '';
    public string $meta_description = '';

    public bool $is_featured = false;

    public string $image_path = '';
    public $image = null;
    public bool $remove_image = false;

    public function mount(): void
    {
        $this->syncFromPost();
    }

    public function updatedPostId(): void
    {
        $this->syncFromPost();
    }

    public function post(): ?Post
    {
        if (! $this->postId) {
            return null;
        }

        return Post::query()->with('tags')->find($this->postId);
    }

    protected function syncFromPost(): void
    {
        $post = $this->post();

        if (! $post) {
            return;
        }

        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->excerpt = $post->excerpt ?? '';
        $this->content = $post->content;
        $this->categoryId = (string) ($post->category_id ?? '');
        $this->status = $post->status;
        $this->publishedAtJalali = $post->published_at ? Jalali::date($post->published_at) : '';
        $this->meta_title = $post->meta_title ?? '';
        $this->meta_description = $post->meta_description ?? '';
        $this->is_featured = $post->is_featured;
        $this->image_path = $post->image_path ?? '';

        $this->tagsInput = $post->tags->pluck('name')->implode(', ');
    }

    protected function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($this->slug ?: $title, '-');

        if ($slug === '') {
            $slug = 'post-' . Str::lower(Str::random(6));
        }

        $base = $slug;
        $index = 2;

        while (
            Post::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $index;
            $index++;
        }

        return $slug;
    }

    protected function syncTags(Post $post): void
    {
        $names = collect(explode(',', $this->tagsInput))
            ->map(fn (string $name): string => trim($name))
            ->filter()
            ->unique()
            ->values();

        $tagIds = [];

        foreach ($names as $name) {
            $slug = Str::slug($name, '-');

            if ($slug === '') {
                continue;
            }

            $tag = Tag::query()->firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );

            $tagIds[] = $tag->id;
        }

        $post->tags()->sync($tagIds);
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

        $path = $this->image->store('posts', 'public');

        if ($this->image_path) {
            Storage::disk('public')->delete($this->image_path);
        }

        $this->image_path = $path;
        $this->image = null;
    }

    public function setJalaliToday(string $property): void
    {
        $this->{$property} = Jalali::today();
    }

    public function clearJalali(string $property): void
    {
        $this->{$property} = '';
    }

    public function save()
    {
        $this->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', 'alpha_dash'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string', 'min:50'],
            'categoryId' => ['nullable', 'numeric', 'exists:categories,id'],
            'tagsInput' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(Post::statusOptions()))],
            'publishedAtJalali' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (trim((string) $value) !== '' && ! Jalali::parse($value)) {
                        $fail('تاریخ انتشار جلالی معتبر نیست.');
                    }
                },
            ],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'title.required' => 'عنوان مقاله را وارد کن.',
            'content.required' => 'محتوای مقاله را بنویس.',
            'content.min' => 'محتوای مقاله باید حداقل ۵۰ کاراکتر باشد.',
            'status.in' => 'وضعیت معتبر نیست.',
            'categoryId.exists' => 'دسته‌بندی انتخابی معتبر نیست.',
            'image.image' => 'تصویر شاخص باید فایل تصویری باشد.',
            'image.max' => 'حجم تصویر نباید بیشتر از ۲ مگابایت باشد.',
        ]);

        $this->handleImageUpload();

        $publishedAt = null;

        if (trim($this->publishedAtJalali) !== '') {
            $publishedAt = Jalali::parse($this->publishedAtJalali);
        }

        if ($this->status === 'published' && ! $publishedAt) {
            $publishedAt = now();
        }

        $data = [
            'title' => trim($this->title),
            'slug' => $this->generateSlug(trim($this->title), $this->postId),
            'excerpt' => trim($this->excerpt) ?: null,
            'content' => trim($this->content),
            'category_id' => $this->categoryId !== '' ? (int) $this->categoryId : null,
            'author_id' => auth()->id(),
            'status' => $this->status,
            'published_at' => $publishedAt,
            'meta_title' => trim($this->meta_title) ?: null,
            'meta_description' => trim($this->meta_description) ?: null,
            'is_featured' => $this->is_featured,
            'image_path' => $this->image_path ?: null,
        ];

        if ($this->postId) {
            $post = $this->post();

            if (! $post) {
                $this->dispatch('rx-toast', [
                    'type' => 'error',
                    'message' => 'مقاله مورد نظر پیدا نشد.',
                ]);

                return redirect()->route('admin.posts.index');
            }

            $post->update($data);
        } else {
            $post = Post::create($data);
            $this->postId = $post->id;
        }

        $this->syncTags($post);

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $data['status'] === 'published'
                ? 'مقاله ذخیره و برای انتشار آماده شد.'
                : 'مقاله به‌عنوان پیش‌نویس ذخیره شد.',
        ]);

        return redirect()->route('admin.posts.index');
    }
};

?>

@php
    $post = $this->post();
    $isEditing = (bool) $post;

    try {
        $categories = Category::activeOptions();
    } catch (\Throwable $e) {
        $categories = [];
    }
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <a href="{{ route('admin.posts.index') }}" class="rx-back-link">
                <i class="fa-solid fa-arrow-right"></i>
                بازگشت به مقالات
            </a>

            <h1>{{ $isEditing ? 'ویرایش مقاله' : 'مقاله جدید' }}</h1>
            <p>عنوان، محتوا، دسته‌بندی، تگ، تصویر و سئوی مقاله را تنظیم کن.</p>
        </div>
    </div>

    <form wire:submit="save" class="rx-edit-form-grid">
        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>محتوای مقاله</h2>
            </div>

            <div class="rx-edit-form-inner">
                <x-admin.form-errors />

                <div class="admin-field">
                    <label class="admin-label">عنوان مقاله</label>
                    <input type="text" wire:model.live="title" class="admin-input" required>
                    @error('title') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">اسلاگ</label>
                    <input type="text" wire:model.live="slug" class="admin-input" dir="ltr" placeholder="how-to-choose-a-web-design-agency">
                    <p class="admin-help">اگر خالی بگذاری، از عنوان ساخته می‌شود.</p>
                    @error('slug') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">خلاصه مقاله</label>
                    <textarea
                        wire:model.live="excerpt"
                        class="admin-input rx-textarea"
                        rows="3"
                        placeholder="یک یا دو خط که در فهرست بلاگ و نتایج گوگل نمایش داده شود."
                    ></textarea>
                    <p class="admin-help">اگر خالی بگذاری، از ابتدای محتوا استفاده می‌شود.</p>
                    @error('excerpt') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">محتوای مقاله</label>
                    <textarea
                        wire:model.live="content"
                        class="admin-input rx-textarea rx-post-editor"
                        rows="20"
                        placeholder="محتوای مقاله را بنویس. می‌توانی از تگ‌های HTML ساده مثل h2, p, ul, li, strong, em, a, img استفاده کنی."
                        required
                    ></textarea>
                    <p class="admin-help">
                        برای عنوان‌های داخلی از `<h2>` و `<h3>` استفاده کن.
                    </p>
                    @error('content') <p class="admin-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>انتشار و سئو</h2>
            </div>

            <div class="rx-edit-form-inner">
                <div class="admin-field">
                    <label class="admin-label">دسته‌بندی</label>

                    @if (! empty($categories))
                        <select wire:model.live="categoryId" class="admin-input">
                            <option value="">بدون دسته‌بندی</option>

                            @foreach ($categories as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" class="admin-input" value="اول یک دسته‌بندی بساز" disabled>
                        <p class="admin-help">
                            برای ساخت دسته‌بندی به صفحه
                            <a href="{{ route('admin.categories') }}" class="text-sky">دسته‌بندی‌ها</a>
                            برو.
                        </p>
                    @endif

                    @error('categoryId') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">تگ‌ها</label>
                    <input
                        type="text"
                        wire:model.live="tagsInput"
                        class="admin-input"
                        placeholder="طراحی وب, سئو, دیجیتال مارکتینگ"
                    >
                    <p class="admin-help">تگ‌ها را با کاما جدا کن.</p>
                    @error('tagsInput') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <x-admin.choice-pills
                    name="status"
                    label="وضعیت انتشار"
                    :options="\App\Models\Post::statusOptions()"
                    :value="$status"
                />

                <x-admin.jalali-input
                    name="publishedAtJalali"
                    label="تاریخ انتشار"
                    :value="$publishedAtJalali"
                    help="اگر خالی بگذاری و وضعیت منتشرشده باشد، زمان الان ثبت می‌شود."
                />

                <label class="admin-checkbox rx-switch-card">
                    <input type="checkbox" wire:model.live="is_featured">
                    <span>مقاله ویژه / featured</span>
                </label>

                <div class="admin-field">
                    <label class="admin-label">تصویر شاخص</label>

                    @if ($image_path)
                        <div class="rx-upload-current">
                            <img src="{{ Storage::disk('public')->url($image_path) }}" alt="تصویر شاخص">
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
                    @error('image') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">Meta Title</label>
                    <input type="text" wire:model.live="meta_title" class="admin-input">
                    @error('meta_title') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">Meta Description</label>
                    <textarea wire:model.live="meta_description" class="admin-input rx-textarea" rows="3"></textarea>
                    @error('meta_description') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="rx-edit-actions">
                    <button type="submit" class="admin-btn admin-btn-primary">
                        <span wire:loading.remove wire:target="save">
                            <i class="fa-solid fa-floppy-disk"></i>
                            ذخیره مقاله
                        </span>
                        <span wire:loading wire:target="save">
                            در حال ذخیره...
                        </span>
                    </button>

                    <a href="{{ route('admin.posts.index') }}" class="admin-btn admin-btn-ghost">
                        انصراف
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>