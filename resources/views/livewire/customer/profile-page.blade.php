<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $phone = '';

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public bool $saved = false;

    public function mount(): void
    {
        $user = auth()->user();

        $this->name = (string) ($user->name ?? '');
        $this->email = (string) ($user->email ?? '');
        $this->phone = (string) ($user->phone ?? '');
    }

    public function save()
    {
        $user = auth()->user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'min:10', 'max:20'],
            'current_password' => ['required_with:password', 'string'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ], [
            'name.required' => 'نام خود را وارد کنید.',
            'name.min' => 'نام باید حداقل ۳ کاراکتر باشد.',
            'email.required' => 'ایمیل را وارد کنید.',
            'email.email' => 'ایمیل معتبر نیست.',
            'email.unique' => 'این ایمیل قبلاً توسط کاربر دیگری استفاده شده است.',
            'phone.min' => 'شماره تماس معتبر نیست.',
            'phone.max' => 'شماره تماس معتبر نیست.',
            'current_password.required_with' => 'برای تغییر رمز عبور، رمز فعلی را وارد کنید.',
            'password.confirmed' => 'تکرار رمز عبور مطابق نیست.',
            'password.min' => 'رمز عبور جدید باید حداقل ۸ کاراکتر باشد.',
        ]);

        if (! empty($validated['password'])) {
            if (! Hash::check($validated['current_password'], $user->password)) {
                $this->addError('current_password', 'رمز عبور فعلی درست نیست.');

                return;
            }
        }

        $user->name = trim($validated['name']);
        $user->email = trim($validated['email']);

        $user->forceFill([
            'phone' => trim($validated['phone'] ?? '') ?: null,
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->saved = true;
    }
};

?>

@php
    $user = auth()->user();
    $siteName = setting('site.name', 'رندیکس');
@endphp

<div>
    <section class="pt-36 pb-24 px-5 md:px-10">
        <div class="max-w-3xl mx-auto">

            <div class="space-y-5 mb-10">
                <div class="badge">
                    <span class="badge-dot"></span>
                    پروفایل کاربری
                </div>

                <h1 class="text-3xl md:text-5xl font-black leading-[1.25]">
                    تنظیمات حساب {{ $siteName }}
                </h1>

                <p class="text-snow/60 leading-8">
                    اطلاعات تماس و رمز عبور خود را مدیریت کن. این اطلاعات برای پیگیری درخواست‌های پروژه استفاده می‌شود.
                </p>
            </div>

            @if ($saved)
                <div class="glass p-5 mb-6 rounded-2xl border border-emerald-400/25 bg-emerald-400/10 text-emerald-200 text-sm leading-7">
                    <i class="fa-solid fa-circle-check"></i>
                    تغییرات با موفقیت ذخیره شد.
                </div>
            @endif

            <form wire:submit="save" class="glass p-7 md:p-9 space-y-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-snow/70">نام و نام خانوادگی *</label>
                    <input
                        type="text"
                        wire:model="name"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:border-sky/60 transition-colors"
                        required
                    >
                    @error('name') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-snow/70">ایمیل *</label>
                    <input
                        type="email"
                        wire:model="email"
                        dir="ltr"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:border-sky/60 transition-colors"
                        required
                    >
                    @error('email') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-snow/70">شماره تماس</label>
                    <input
                        type="tel"
                        wire:model="phone"
                        dir="ltr"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:border-sky/60 transition-colors"
                    >
                    @error('phone') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 border-t border-white/10 space-y-5">
                    <h2 class="text-lg font-black">تغییر رمز عبور</h2>

                    <p class="text-xs text-snow/45 leading-7">
                        اگر نمی‌خواهی رمز عبور را تغییر دهی، این بخش را خالی بگذار.
                    </p>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-snow/70">رمز عبور فعلی</label>
                        <input
                            type="password"
                            wire:model="current_password"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:border-sky/60 transition-colors"
                        >
                        @error('current_password') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-snow/70">رمز عبور جدید</label>
                            <input
                                type="password"
                                wire:model="password"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:border-sky/60 transition-colors"
                            >
                            @error('password') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-snow/70">تکرار رمز عبور جدید</label>
                            <input
                                type="password"
                                wire:model="password_confirmation"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:border-sky/60 transition-colors"
                            >
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full !py-4" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        ذخیره تغییرات
                        <i class="fa-solid fa-floppy-disk"></i>
                    </span>
                    <span wire:loading>در حال ذخیره...</span>
                </button>
            </form>
        </div>
    </section>
</div>