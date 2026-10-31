<?php

use App\Models\Template;
use App\Models\TemplateFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {
    use WithFileUploads;

    #[Url(as: 'id')]
    public ?int $templateId = null;

    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public string $features = '';
    public string $price = '';

    public bool $has_support = false;
    public bool $has_trial = false;
    public bool $has_guarantee = false;
    public bool $is_fullstack = false;
    public bool $is_active = true;

    public array $languages = [''];
    public $uploadedFiles = [];

    public function mount(): void
    {
        $this->sync();
    }

    public function updatedTemplateId(): void
    {
        $this->sync();
    }

    public function template(): ?Template
    {
        if (! $this->templateId) {
            return null;
        }

        try {
            return Template::query()->with(['languages', 'files'])->find($this->templateId);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function sync(): void
    {
        $t = $this->template();

        if (! $t) {
            return;
        }

        $this->name = $t->name;
        $this->slug = $t->slug;
        $this->description = $t->description ?? '';
        $this->features = is_array($t->features) ? implode("\n", $t->features) : (string) ($t->features ?? '');
        $this->price = (string) $t->price;
        $this->has_support = $t->has_support;
        $this->has_trial = $t->has_trial;
        $this->has_guarantee = $t->has_guarantee;
        $this->is_fullstack = $t->is_fullstack;
        $this->is_active = $t->is_active;

        $langs = $t->languages->pluck('language')->all();
        $this->languages = empty($langs) ? [''] : $langs;
    }

    public function updatedName(string $value): void
    {
        if (! $this->templateId) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedPrice($value): void
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $value);
        $num = (int) $digits;
        $num = (int) (round($num / 1000) * 1000);
        $this->price = (string) $num;
    }

    public function addLanguage(): void
    {
        $this->languages[] = '';
    }

    public function removeLanguage(int $index): void
    {
        unset($this->languages[$index]);
        $this->languages = array_values($this->languages);

        if (empty($this->languages)) {
            $this->languages = [''];
        }
    }

    protected function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base !== '' ? $base : 'template-' . Str::lower(Str::random(6));
        $candidate = $slug;
        $i = 2;

        while (
            Template::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $slug . '-' . $i;
            $i++;
        }

        return $candidate;
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'slug' => ['required', 'string', 'min:3', 'max:255', 'alpha_dash'],
            'price' => ['required', 'numeric', 'min:1000'],
        ], [
            'name.required' => 'نام قالب را وارد کن.',
            'slug.required' => 'Slug را وارد کن.',
            'slug.alpha_dash' => 'Slug فقط حروف انگلیسی، عدد، خط تیره و زیرخط.',
            'price.required' => 'قیمت را وارد کن.',
            'price.min' => 'قیمت باید حداقل ۱,۰۰۰ تومان باشد.',
        ]);

        $featuresArray = array_values(array_filter(array_map('trim', explode("\n", $this->features))));

        $data = [
            'name' => trim($this->name),
            'slug' => $this->uniqueSlug(trim($this->slug), $this->templateId),
            'description' => trim($this->description) ?: null,
            'features' => $featuresArray,
            'price' => (int) $this->price,
            'has_support' => $this->has_support,
            'has_trial' => $this->has_trial,
            'has_guarantee' => $this->has_guarantee,
            'is_fullstack' => $this->is_fullstack,
            'is_active' => $this->is_active,
        ];

        if ($this->templateId) {
            $t = $this->template();

            if (! $t) {
                $this->dispatch('rx-toast', ['type' => 'error', 'message' => 'قالب پیدا نشد.']);

                return redirect()->route('admin.templates.index');
            }

            $t->update($data);
        } else {
            $t = Template::create($data);
            $this->templateId = $t->id;
        }

        $t->languages()->delete();

        foreach (array_unique(array_filter(array_map('trim', $this->languages))) as $lang) {
            $t->languages()->create(['language' => $lang, 'color' => $this->langColor($lang)]);
        }

        foreach ($this->uploadedFiles as $file) {
            $path = $file->store('templates/' . $t->id, 'public');

            TemplateFile::create([
                'template_id' => $t->id,
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'is_demo' => false,
            ]);
        }

        $this->uploadedFiles = [];

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'قالب ذخیره شد. حالا می‌توانی فایل دمو را ست کنی.',
        ]);

        return redirect()->route('admin.templates.form', ['id' => $t->id]);
    }

    public function setDemoFile(int $fileId): void
    {
        $t = $this->template();

        if (! $t) {
            return;
        }

        $t->files()->update(['is_demo' => false]);
        $t->files()->where('id', $fileId)->update(['is_demo' => true]);

        $this->dispatch('rx-toast', ['type' => 'success', 'message' => 'فایل دمو تنظیم شد؛ دکمه «دمو زنده» فعال شد.']);
    }

    public function deleteFile(int $fileId): void
    {
        $file = TemplateFile::find($fileId);

        if (! $file) {
            return;
        }

        try {
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }
        } catch (\Throwable $e) {}

        $file->delete();

        $this->dispatch('rx-toast', ['type' => 'success', 'message' => 'فایل حذف شد.']);
    }

    protected function langColor(string $language): string
    {
        $colors = [
            'React' => 'rgba(97,218,251,.18)', 'Vue' => 'rgba(65,184,131,.18)',
            'Angular' => 'rgba(221,0,49,.18)', 'Laravel' => 'rgba(255,45,32,.18)',
            'PHP' => 'rgba(119,123,180,.18)', 'JavaScript' => 'rgba(247,223,30,.18)',
            'TypeScript' => 'rgba(49,120,198,.18)', 'HTML' => 'rgba(227,79,38,.18)',
            'CSS' => 'rgba(86,61,124,.18)', 'Tailwind' => 'rgba(56,189,248,.18)',
            'Bootstrap' => 'rgba(121,82,179,.18)', 'Python' => 'rgba(53,114,165,.18)',
            'Node.js' => 'rgba(104,159,56,.18)', 'Next.js' => 'rgba(255,255,255,.18)',
            'Nuxt' => 'rgba(0,220,130,.18)', 'Svelte' => 'rgba(255,62,0,.18)',
            'Django' => 'rgba(9,76,56,.18)', 'PostgreSQL' => 'rgba(51,103,146,.18)',
        ];

        return $colors[$language] ?? 'rgba(69,200,255,.15)';
    }
};

?>

@php
    $template = $this->template();
    $files = $template ? $template->files : collect();
    $priceNumber = (int) $price;
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <a href="{{ route('admin.templates.index') }}" class="rx-back-link">
                <i class="fa-solid fa-arrow-right"></i> بازگشت به فروشگاه قالب
            </a>
            <h1>{{ $template ? 'ویرایش قالب' : 'افزودن قالب جدید' }}</h1>
            <p>اطلاعات، زبان‌ها، قیمت و فایل‌های قالب را مدیریت کن</p>
        </div>
    </div>

    <form wire:submit="save" class="rx-edit-form-grid">
        <div class="admin-panel rx-edit-form-inner">
            <x-admin.form-errors />

            <div class="admin-field">
                <label class="admin-label">نام قالب <span class="admin-required">*</span></label>
                <input type="text" wire:model.blur="name" class="admin-input" placeholder="مثال: قالب فروشگاهی React + Laravel">
                @error('name')<p class="admin-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="admin-field">
                <label class="admin-label">Slug (لینک) <span class="admin-required">*</span></label>
                <input type="text" wire:model.blur="slug" class="admin-input" dir="ltr" placeholder="react-shop-template">
                @error('slug')<p class="admin-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="admin-field">
                <label class="admin-label">توضیحات کامل</label>
                <textarea wire:model="description" class="admin-input rx-textarea" rows="6" placeholder="درباره قالب، کاربرد و مزیت‌ها بنویس..."></textarea>
            </div>

            <div class="admin-field">
                <label class="admin-label">امکانات قالب (هر خط یک امکان)</label>
                <textarea wire:model="features" class="admin-input rx-textarea" rows="6" placeholder="طراحی ریسپانسیو&#10;پنل مدیریت کامل&#10;سئو بهینه‌شده"></textarea>
                <p class="admin-help">هر خط، یک آیتم در لیست امکانات صفحه قالب می‌شود.</p>
            </div>

            <div class="admin-field">
                <label class="admin-label">قیمت (تومان) <span class="admin-required">*</span></label>
                <input type="text" inputmode="numeric" wire:model.live="price" class="admin-input" dir="ltr" placeholder="2000000">
                <p class="admin-help">
                    نمایش برای مشتری:
                    <strong style="color:var(--rx-sky);">{{ number_format($priceNumber, 0, '.', ',') }} تومان</strong>
                    — خودکار به نزدیک‌ترین هزار تومان گرد می‌شود.
                </p>
                @error('price')<p class="admin-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="rx-two-col">
                <label class="admin-checkbox rx-switch-card"><input type="checkbox" wire:model="has_support"><span>پشتیبانی فنی دارد</span></label>
                <label class="admin-checkbox rx-switch-card"><input type="checkbox" wire:model="has_trial"><span>مهلت تست دارد</span></label>
                <label class="admin-checkbox rx-switch-card"><input type="checkbox" wire:model="has_guarantee"><span>ضمانت بازگشت وجه</span></label>
                <label class="admin-checkbox rx-switch-card"><input type="checkbox" wire:model="is_fullstack"><span>Full-Stack (با بک‌اند)</span></label>
                <label class="admin-checkbox rx-switch-card" style="grid-column:1/-1;"><input type="checkbox" wire:model="is_active"><span>فعال (در فروشگاه نمایش داده شود)</span></label>
            </div>
        </div>

        <div class="admin-panel rx-edit-form-inner">
            <div class="admin-field">
                <label class="admin-label">زبان‌های برنامه‌نویسی</label>

                <div style="display:grid;gap:10px;">
                    @foreach ($languages as $index => $lang)
                        <div style="display:flex;gap:8px;align-items:center;">
                            <input type="text" wire:model="languages.{{ $index }}" class="admin-input" list="rx-lang-suggestions" placeholder="React / Laravel / Vue ...">
                            @if (count($languages) > 1)
                                <button type="button" wire:click="removeLanguage({{ $index }})" class="admin-icon-btn danger" title="حذف"><i class="fa-solid fa-trash"></i></button>
                            @endif
                        </div>
                    @endforeach

                    <datalist id="rx-lang-suggestions">
                        <option value="React"></option><option value="Vue"></option><option value="Angular"></option>
                        <option value="Svelte"></option><option value="Laravel"></option><option value="PHP"></option>
                        <option value="JavaScript"></option><option value="TypeScript"></option><option value="Next.js"></option>
                        <option value="Nuxt"></option><option value="Tailwind"></option><option value="Bootstrap"></option>
                        <option value="HTML"></option><option value="CSS"></option><option value="Python"></option>
                        <option value="Django"></option><option value="Node.js"></option><option value="PostgreSQL"></option>
                    </datalist>

                    <button type="button" wire:click="addLanguage" class="admin-btn admin-btn-ghost">
                        <i class="fa-solid fa-plus"></i> افزودن زبان
                    </button>
                </div>

                <p class="admin-help">رنگ badge هر زبان به‌صورت خودکار انتخاب می‌شود.</p>
            </div>

            @if ($template)
                <div style="border-top:1px solid var(--rx-border);padding-top:18px;display:grid;gap:12px;">
                    <label class="admin-label">فایل‌های قالب</label>

                    @forelse ($files as $file)
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px;background:rgba(255,255,255,.035);border:1px solid rgba(255,255,255,.08);border-radius:14px;">
                            <div style="min-width:0;">
                                <strong style="font-size:13px;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $file->filename }}</strong>
                                <small style="display:block;color:var(--rx-soft);font-size:11px;margin-top:3px;">
                                    {{ $file->humanSize() }}
                                    @if ($file->is_demo)<span class="admin-badge success" style="margin-inline-start:6px;"><i class="fa-solid fa-play"></i> دمو</span>@endif
                                </small>
                            </div>
                            <div style="display:flex;gap:6px;flex-shrink:0;">
                                @if (! $file->is_demo)
                                    <button type="button" wire:click="setDemoFile({{ $file->id }})" class="admin-icon-btn" title="ست‌کردن به‌عنوان دمو زنده"><i class="fa-solid fa-star"></i></button>
                                @endif
                                <button type="button" wire:click="deleteFile({{ $file->id }})" wire:confirm="این فایل حذف شود؟" class="admin-icon-btn danger" title="حذف"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    @empty
                        <p class="admin-help">هنوز فایلی آپلود نشده. بعد از ذخیره‌ی اول، می‌توانی فایل‌ها را آپلود کنی.</p>
                    @endforelse

                    <div class="admin-field">
                        <label class="admin-label">آپلود فایل جدید (چندتایی)</label>
                        <input type="file" wire:model="uploadedFiles" multiple class="admin-input" accept=".html,.htm,.css,.js,.jsx,.ts,.tsx,.vue,.svelte,.php,.json,.zip,.rar,.tar,.gz">
                        <div wire:loading wire:target="uploadedFiles" style="color:var(--rx-sky);font-size:12px;"><i class="fa-solid fa-spinner fa-spin"></i> در حال آپلود...</div>
                        <p class="admin-help">برای دمو زنده: فایل HTML اصلی را آپلود کن و ستاره بزن تا «دمو» شود.</p>
                        @error('uploadedFiles.*')<p class="admin-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                    </div>
                </div>
            @endif

            <div class="rx-edit-actions">
                <button type="submit" class="admin-btn admin-btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    {{ $template ? 'ذخیره تغییرات' : 'ایجاد قالب' }}
                </button>
            </div>
        </div>
    </form>
</div>