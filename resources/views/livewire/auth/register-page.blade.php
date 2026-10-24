<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register()
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'min:10', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required' => 'نام خود را وارد کنید.',
            'name.min' => 'نام باید حداقل ۳ کاراکتر باشد.',
            'email.required' => 'ایمیل را وارد کنید.',
            'email.email' => 'ایمیل معتبر نیست.',
            'email.unique' => 'این ایمیل قبلاً ثبت شده است.',
            'phone.min' => 'شماره تماس معتبر نیست.',
            'phone.max' => 'شماره تماس معتبر نیست.',
            'password.required' => 'رمز عبور را وارد کنید.',
            'password.confirmed' => 'تکرار رمز عبور مطابق نیست.',
            'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => trim($validated['email']),
            'password' => Hash::make($validated['password']),
        ]);

        $user->forceFill([
            'role' => 'customer',
            'phone' => trim($validated['phone'] ?? '') ?: null,
        ])->save();

        Auth::login($user);

        request()->session()->regenerate();

        return redirect()->route('dashboard');
    }
};

?>

@php
    $siteName = setting('site.name', 'رندیکس');
@endphp

<div>
    <section class="pt-36 pb-24 px-5 md:px-10 min-h-screen">
        <div class="max-w-xl mx-auto">

            <div class="text-center space-y-5 mb-10">
                <div class="badge inline-flex">
                    <span class="badge-dot"></span>
                    حساب کاربری {{ $siteName }}
                </div>

                <h1 class="text-4xl md:text-5xl font-black leading-[1.25]">
                    ثبت‌نام
                    <span class="gradient-text">مشتری</span>
                </h1>

                <p class="text-snow/60 leading-8">
                    با ساخت حساب کاربری، می‌توانی درخواست‌های پروژه، کدهای پیگیری و وضعیت همکاری‌هایت را در داشبورد شخصی دنبال کنی.
                </p>
            </div>

            <form wire:submit="register" class="glass p-7 md:p-9 space-y-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-snow/70">نام و نام خانوادگی *</label>
                    <input
                        type="text"
                        wire:model="name"
                        placeholder="مثلاً: علی رضایی"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                        required
                    >
                    @error('name') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-snow/70">ایمیل *</label>
                    <input
                        type="email"
                        wire:model="email"
                        placeholder="you@example.com"
                        dir="ltr"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                        required
                    >
                    @error('email') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-snow/70">شماره تماس</label>
                    <input
                        type="tel"
                        wire:model="phone"
                        placeholder="09xxxxxxxxx"
                        dir="ltr"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                    >
                    @error('phone') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-snow/70">رمز عبور *</label>
                        <input
                            type="password"
                            wire:model="password"
                            placeholder="حداقل ۸ کاراکتر"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                            required
                        >
                        @error('password') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-snow/70">تکرار رمز عبور *</label>
                        <input
                            type="password"
                            wire:model="password_confirmation"
                            placeholder="تکرار رمز عبور"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                            required
                        >
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full !py-4" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        ساخت حساب کاربری
                        <i class="fa-solid fa-user-plus"></i>
                    </span>
                    <span wire:loading>در حال ثبت‌نام...</span>
                </button>

                <p class="text-sm text-snow/55 text-center leading-7">
                    حساب داری؟
                    <a href="{{ route('login') }}" class="text-sky font-bold hover:text-sky/80 transition-colors">
                        وارد شو
                    </a>
                </p>
            </form>
        </div>
    </section>
</div>