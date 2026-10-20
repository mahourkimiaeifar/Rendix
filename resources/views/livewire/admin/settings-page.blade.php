<?php

use App\Support\MaintenanceMode;
use App\Support\Settings;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {
    use WithFileUploads;

    public string $site_name = '';
    public string $tagline = '';

    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $working_hours = '';

    public string $instagram = '';
    public string $telegram = '';
    public string $whatsapp = '';
    public string $linkedin = '';
    public string $youtube = '';
    public string $twitter = '';

    public bool $contact_enabled = true;
    public string $contact_success_message = '';

    public string $maintenance_message = '';
    public string $maintenance_telegram = '';

    public string $logo_path = '';
    public string $favicon_path = '';
    public string $og_image_path = '';

    public $logo = null;
    public $favicon = null;
    public $og_image = null;

    public bool $remove_logo = false;
    public bool $remove_favicon = false;
    public bool $remove_og_image = false;

    public function mount(): void
    {
        $this->loadSettings();
    }

    protected function loadSettings(): void
    {
        $maintenance = MaintenanceMode::data();

        $this->site_name = (string) Settings::get('site.name', 'رندیکس');
        $this->tagline = (string) Settings::get('site.tagline', 'طراحی وبسایت، سئو و دیجیتال مارکتینگ');

        $this->phone = (string) Settings::get('contact.phone', '');
        $this->email = (string) Settings::get('contact.email', '');
        $this->address = (string) Settings::get('contact.address', '');
        $this->working_hours = (string) Settings::get('contact.working_hours', '');

        $this->instagram = (string) Settings::get('social.instagram', '');
        $this->telegram = (string) Settings::get('social.telegram', 'https://t.me/RendixDMServices');
        $this->whatsapp = (string) Settings::get('social.whatsapp', '');
        $this->linkedin = (string) Settings::get('social.linkedin', '');
        $this->youtube = (string) Settings::get('social.youtube', '');
        $this->twitter = (string) Settings::get('social.twitter', '');

        $this->contact_enabled = (bool) Settings::get('contact.enabled', true);
        $this->contact_success_message = (string) Settings::get('contact.success_message', 'پیام شما با موفقیت ثبت شد. تیم رندیکس به‌زودی با شما تماس می‌گیرد.');

        $this->maintenance_message = (string) Settings::get('maintenance.message', $maintenance['message'] ?? 'ما داریم موتور رندیکس را کوک می‌کنیم؛ چند دقیقه دیگر با سرعت، دقت و ظاهر بهتر برمی‌گردیم.');
        $this->maintenance_telegram = (string) Settings::get('maintenance.telegram', $maintenance['telegram'] ?? 'https://t.me/RendixDMServices');

        $this->logo_path = (string) Settings::get('brand.logo', '');
        $this->favicon_path = (string) Settings::get('brand.favicon', '');
        $this->og_image_path = (string) Settings::get('brand.og_image', '');
    }

    protected function handleUpload(string $property, string $pathProperty, string $folder): void
    {
        if ($this->{$property} === null) {
            return;
        }

        $path = $this->{$property}->store($folder, 'public');

        $oldPath = $this->{$pathProperty};

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $this->{$pathProperty} = $path;
        $this->{$property} = null;
    }

    protected function handleRemove(string $pathProperty, string $removeProperty): void
    {
        if (! $this->{$removeProperty}) {
            return;
        }

        $oldPath = $this->{$pathProperty};

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $this->{$pathProperty} = '';
        $this->{$removeProperty} = false;
    }

    public function save(): void
    {
        $this->validate([
            'site_name' => ['required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:255'],

            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:190'],
            'address' => ['nullable', 'string', 'max:500'],
            'working_hours' => ['nullable', 'string', 'max:255'],

            'instagram' => ['nullable', 'url', 'max:255'],
            'telegram' => ['nullable', 'url', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'youtube' => ['nullable', 'url', 'max:255'],
            'twitter' => ['nullable', 'url', 'max:255'],

            'contact_success_message' => ['nullable', 'string', 'max:500'],
            'maintenance_message' => ['nullable', 'string', 'max:500'],
            'maintenance_telegram' => ['nullable', 'url', 'max:255'],

            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,ico', 'max:1024'],
            'og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'site_name.required' => 'نام برند را وارد کن.',
            'email.email' => 'ایمیل معتبر نیست.',
            'instagram.url' => 'لینک اینستاگرام معتبر نیست.',
            'telegram.url' => 'لینک تلگرام معتبر نیست.',
            'linkedin.url' => 'لینک لینکدین معتبر نیست.',
            'youtube.url' => 'لینک یوتیوب معتبر نیست.',
            'twitter.url' => 'لینک توییتر/X معتبر نیست.',
            'maintenance_telegram.url' => 'لینک تلگرام حالت تعمیرات معتبر نیست.',
            'logo.image' => 'لوگو باید تصویر باشد.',
            'logo.mimes' => 'لوگو فقط JPG, PNG, WEBP مجاز است.',
            'logo.max' => 'حجم لوگو نباید بیشتر از ۲ مگابایت باشد.',
            'favicon.max' => 'حجم favicon نباید بیشتر از ۱ مگابایت باشد.',
            'og_image.max' => 'حجم تصویر OG نباید بیشتر از ۲ مگابایت باشد.',
        ]);

        $this->handleRemove('logo_path', 'remove_logo');
        $this->handleRemove('favicon_path', 'remove_favicon');
        $this->handleRemove('og_image_path', 'remove_og_image');

        $this->handleUpload('logo', 'logo_path', 'brand');
        $this->handleUpload('favicon', 'favicon_path', 'brand');
        $this->handleUpload('og_image', 'og_image_path', 'brand');

        Settings::setMany([
            'site.name' => trim($this->site_name),
            'site.tagline' => trim($this->tagline),

            'contact.phone' => trim($this->phone),
            'contact.email' => trim($this->email),
            'contact.address' => trim($this->address),
            'contact.working_hours' => trim($this->working_hours),

            'social.instagram' => trim($this->instagram),
            'social.telegram' => trim($this->telegram),
            'social.whatsapp' => trim($this->whatsapp),
            'social.linkedin' => trim($this->linkedin),
            'social.youtube' => trim($this->youtube),
            'social.twitter' => trim($this->twitter),

            'contact.enabled' => $this->contact_enabled,
            'contact.success_message' => trim($this->contact_success_message),

            'maintenance.message' => trim($this->maintenance_message),
            'maintenance.telegram' => trim($this->maintenance_telegram),

            'brand.logo' => $this->logo_path,
            'brand.favicon' => $this->favicon_path,
            'brand.og_image' => $this->og_image_path,
        ]);

        MaintenanceMode::toggle(
            MaintenanceMode::enabled(),
            trim($this->maintenance_message),
            trim($this->maintenance_telegram)
        );

        $this->loadSettings();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'تنظیمات سایت با موفقیت ذخیره شد.',
        ]);
    }
};

?>

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>تنظیمات سایت</h1>
            <p>هویت برند، اطلاعات تماس، شبکه‌های اجتماعی، فرم تماس و حالت تعمیرات را مدیریت کن.</p>
        </div>
    </div>

    <form wire:submit="save" class="rx-settings-stack">
        <x-admin.form-errors />

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>هویت برند</h2>
            </div>

            <div class="rx-settings-grid">
                <div class="admin-field">
                    <label class="admin-label">نام برند</label>
                    <input type="text" wire:model.live="site_name" class="admin-input" required>
                    @error('site_name') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">شعار کوتاه</label>
                    <input type="text" wire:model.live="tagline" class="admin-input">
                    @error('tagline') <p class="admin-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>اطلاعات تماس</h2>
            </div>

            <div class="rx-settings-grid">
                <div class="admin-field">
                    <label class="admin-label">تلفن</label>
                    <input type="text" wire:model.live="phone" class="admin-input" dir="ltr">
                    @error('phone') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">ایمیل</label>
                    <input type="email" wire:model.live="email" class="admin-input" dir="ltr">
                    @error('email') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field rx-full-width">
                    <label class="admin-label">آدرس</label>
                    <textarea wire:model.live="address" class="admin-input rx-textarea" rows="3"></textarea>
                    @error('address') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">ساعات کاری</label>
                    <input type="text" wire:model.live="working_hours" class="admin-input" placeholder="شنبه تا پنجشنبه، ۹ تا ۸">
                    @error('working_hours') <p class="admin-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>شبکه‌های اجتماعی</h2>
            </div>

            <div class="rx-settings-grid">
                <div class="admin-field">
                    <label class="admin-label">تلگرام</label>
                    <input type="url" wire:model.live="telegram" class="admin-input" dir="ltr" placeholder="https://t.me/RendixDMServices">
                    @error('telegram') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">واتساپ</label>
                    <input type="text" wire:model.live="whatsapp" class="admin-input" dir="ltr" placeholder="https://wa.me/98...">
                    @error('whatsapp') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">اینستاگرام</label>
                    <input type="url" wire:model.live="instagram" class="admin-input" dir="ltr">
                    @error('instagram') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">لینکدین</label>
                    <input type="url" wire:model.live="linkedin" class="admin-input" dir="ltr">
                    @error('linkedin') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">یوتیوب</label>
                    <input type="url" wire:model.live="youtube" class="admin-input" dir="ltr">
                    @error('youtube') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">توییتر / X</label>
                    <input type="url" wire:model.live="twitter" class="admin-input" dir="ltr">
                    @error('twitter') <p class="admin-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>فرم تماس</h2>
            </div>

            <div class="rx-settings-grid">
                <label class="admin-checkbox rx-switch-card">
                    <input type="checkbox" wire:model.live="contact_enabled">
                    <span>فرم تماس فعال باشد</span>
                </label>

                <div class="admin-field rx-full-width">
                    <label class="admin-label">پیام موفقیت فرم</label>
                    <textarea wire:model.live="contact_success_message" class="admin-input rx-textarea" rows="3"></textarea>
                    @error('contact_success_message') <p class="admin-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>حالت تعمیرات</h2>
            </div>

            <div class="rx-settings-grid">
                <div class="admin-field rx-full-width">
                    <label class="admin-label">پیام صفحه تعمیرات</label>
                    <textarea wire:model.live="maintenance_message" class="admin-input rx-textarea" rows="3"></textarea>
                    @error('maintenance_message') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">لینک تلگرام حالت تعمیرات</label>
                    <input type="url" wire:model.live="maintenance_telegram" class="admin-input" dir="ltr">
                    @error('maintenance_telegram') <p class="admin-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>تصاویر برند</h2>
            </div>

            <div class="rx-upload-grid">
                <div class="rx-upload-card">
                    <label class="admin-label">لوگو</label>

                    @if ($logo_path)
                        <div class="rx-upload-current">
                            <img src="{{ Storage::disk('public')->url($logo_path) }}" alt="لوگو">
                            <label class="admin-checkbox">
                                <input type="checkbox" wire:model.live="remove_logo">
                                <span>حذف لوگو</span>
                            </label>
                        </div>
                    @endif

                    @if ($logo)
                        <div class="rx-upload-preview">
                            <img src="{{ $logo->temporaryUrl() }}" alt="پیش‌نمایش لوگو">
                        </div>
                    @endif

                    <input type="file" wire:model="logo" class="admin-input" accept="image/*">
                    @error('logo') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="rx-upload-card">
                    <label class="admin-label">Favicon</label>

                    @if ($favicon_path)
                        <div class="rx-upload-current">
                            <img src="{{ Storage::disk('public')->url($favicon_path) }}" alt="favicon">
                            <label class="admin-checkbox">
                                <input type="checkbox" wire:model.live="remove_favicon">
                                <span>حذف favicon</span>
                            </label>
                        </div>
                    @endif

                    @if ($favicon)
                        <div class="rx-upload-preview">
                            <img src="{{ $favicon->temporaryUrl() }}" alt="پیش‌نمایش favicon">
                        </div>
                    @endif

                    <input type="file" wire:model="favicon" class="admin-input" accept="image/*,.ico">
                    @error('favicon') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="rx-upload-card">
                    <label class="admin-label">تصویر OG</label>

                    @if ($og_image_path)
                        <div class="rx-upload-current">
                            <img src="{{ Storage::disk('public')->url($og_image_path) }}" alt="تصویر OG">
                            <label class="admin-checkbox">
                                <input type="checkbox" wire:model.live="remove_og_image">
                                <span>حذف تصویر OG</span>
                            </label>
                        </div>
                    @endif

                    @if ($og_image)
                        <div class="rx-upload-preview">
                            <img src="{{ $og_image->temporaryUrl() }}" alt="پیش‌نمایش تصویر OG">
                        </div>
                    @endif

                    <input type="file" wire:model="og_image" class="admin-input" accept="image/*">
                    @error('og_image') <p class="admin-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="rx-form-actions">
            <button type="submit" class="admin-btn admin-btn-primary">
                <span wire:loading.remove wire:target="save">
                    <i class="fa-solid fa-floppy-disk"></i>
                    ذخیره تنظیمات
                </span>
                <span wire:loading wire:target="save">
                    در حال ذخیره...
                </span>
            </button>
        </div>
    </form>
</div>