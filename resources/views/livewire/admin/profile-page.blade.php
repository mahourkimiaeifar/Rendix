<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {
    public string $name = '';

    public string $email = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();

        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
    }

    public function update(): void
    {
        $user = auth()->user();

        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:190',
                Rule::unique('users')->ignore($user->id),
            ],
            'current_password' => ['required', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'نام را وارد کن.',
            'name.max' => 'نام نمی‌تواند بیشتر از ۱۲ کاراکتر باشد.',

            'email.required' => 'ایمیل را وارد کن.',
            'email.email' => 'ایمیل معتبر نیست.',
            'email.unique' => 'این ایمیل قبلاً استفاده شده است.',

            'current_password.required' => 'برای ذخیره تغییرات، رمز فعلی را وارد کن.',

            'password.min' => 'رمز جدید باید حداقل ۸ کاراکتر باشد.',
            'password.confirmed' => 'تکرار رمز جدید مطابق نیست.',
        ]);

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'رمز فعلی اشتباه است.');

            return;
        }

        $user->name = $this->name;
        $user->email = $this->email;

        if ($this->password !== '') {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'پروفایل با موفقیت ذخیره شد.',
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
    }
};

?>

@php
    $user = auth()->user();

    $roleLabels = [
        'super_admin' => 'سوپر ادمین',
        'admin' => 'ادمین',
        'editor' => 'ویرایشگر',
        'sales' => 'مدیر فروش',
        'viewer' => 'بیننده',
    ];
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>پروفایل و امنیت</h1>
            <p>نام، ایمیل و رمز عبور حساب مدیریتی خود را مدیریت کن.</p>
        </div>
    </div>

    <div class="rx-profile-grid">
        <div class="admin-panel rx-profile-summary">
            <div class="rx-profile-summary-inner">
                <div class="rx-profile-avatar">
                    {{ mb_substr($user->name ?? 'ر', 0, 1) }}
                </div>

                <h2>{{ $user->name }}</h2>
                <p dir="ltr">{{ $user->email }}</p>

                <span class="rx-role-badge">
                    {{ $roleLabels[$user->role] ?? $user->role }}
                </span>

                <div class="rx-meta-list">
                    <div>
                        <span>عضو از</span>
                        <strong><x-admin.time :value="$user->created_at" /></strong>
                    </div>

                    <div>
                        <span>آخرین به‌روزرسانی</span>
                        <strong><x-admin.time :value="$user->updated_at" /></strong>
                    </div>

                    <div>
                        <span>سطح دسترسی</span>
                        <strong>{{ $user->isAdmin() ? 'مدیریت کامل' : 'محدود' }}</strong>
                    </div>
                </div>

                <div class="rx-security-note">
                    <i class="fa-solid fa-shield-halved"></i>
                    <p>
                        برای امنیت بیشتر، رمز قوی با حروف بزرگ، کوچک، عدد و علامت استفاده کن.
                        ایمیل مدیریتی را هم فقط خودت دانسته نگه دار.
                    </p>
                </div>
            </div>
        </div>

        <div class="admin-panel rx-profile-form">
            <div class="admin-panel-header">
                <h2>ویرایش حساب</h2>
            </div>

            <form wire:submit="update" class="rx-profile-form-inner">
                <x-admin.form-errors />
                @if ($errors->any())
                    <div class="rx-form-errors" role="alert">
                        <div class="rx-form-errors-icon">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>

                        <div>
                            <strong>لطفاً این موارد رو یه نگاه بنداز:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                <div class="admin-field">
                    <label for="name" class="admin-label">نام نمایشی</label>
                    <input
                        id="name"
                        type="text"
                        wire:model.live="name"
                        class="admin-input"
                        placeholder="ادمین رندیکس"
                        required
                    >
                    @error('name') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label for="email" class="admin-label">ایمیل مدیریتی</label>
                    <input
                        id="email"
                        type="email"
                        wire:model.live="email"
                        class="admin-input"
                        placeholder="admin@rendix.ir"
                        dir="ltr"
                        required
                    >
                    @error('email') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="rx-profile-divider"></div>

                <div class="rx-password-help">
                    <i class="fa-solid fa-key"></i>
                    <p>
                        برای تغییر رمز، رمز فعلی را وارد کن و رمز جدید را دو بار تایپ کن.
                        اگر نمی‌خواهی رمز را عوض کنی، دو فیلد آخر را خالی بگذار.
                    </p>
                </div>

                <div class="admin-field">
                    <label for="current_password" class="admin-label">رمز فعلی</label>

                    <div class="admin-input-shell rx-password-shell" data-rx-password-shell>
                        <input
                            id="current_password"
                            type="password"
                            wire:model.live="current_password"
                            class="admin-input rx-password-input"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="rx-password-toggle admin-password-toggle"
                            data-rx-password-toggle
                            aria-label="نمایش رمز فعلی"
                            aria-pressed="false"
                            title="نمایش رمز"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>

                    @error('current_password')
                        <p class="admin-error">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div class="admin-field">
                    <label for="password" class="admin-label">رمز جدید</label>

                    <div class="admin-input-shell rx-password-shell" data-rx-password-shell>
                        <input
                            id="password"
                            type="password"
                            wire:model.live="password"
                            class="admin-input rx-password-input"
                            placeholder="حداقل ۸ کاراکتر"
                            autocomplete="new-password"
                        >

                        <button
                            type="button"
                            class="rx-password-toggle admin-password-toggle"
                            data-rx-password-toggle
                            aria-label="نمایش رمز جدید"
                            aria-pressed="false"
                            title="نمایش رمز"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>

                    @error('password')
                        <p class="admin-error">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div class="admin-field">
                    <label for="password_confirmation" class="admin-label">تکرار رمز جدید</label>

                    <div class="admin-input-shell rx-password-shell" data-rx-password-shell>
                        <input
                            id="password_confirmation"
                            type="password"
                            wire:model.live="password_confirmation"
                            class="admin-input rx-password-input"
                            placeholder="رمز جدید را دوباره وارد کن"
                            autocomplete="new-password"
                        >

                        <button
                            type="button"
                            class="rx-password-toggle admin-password-toggle"
                            data-rx-password-toggle
                            aria-label="نمایش تکرار رمز جدید"
                            aria-pressed="false"
                            title="نمایش رمز"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="admin-btn admin-btn-primary w-full">
                    <span wire:loading.remove wire:target="update">
                        ذخیره تغییرات
                        <i class="fa-solid fa-floppy-disk"></i>
                    </span>
                    <span wire:loading wire:target="update">
                        در حال ذخیره...
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>