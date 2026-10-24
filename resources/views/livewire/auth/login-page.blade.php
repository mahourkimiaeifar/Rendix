<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $validated = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'ایمیل را وارد کنید.',
            'email.email' => 'ایمیل معتبر نیست.',
            'password.required' => 'رمز عبور را وارد کنید.',
        ]);

        $key = 'rendix-login:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', 'برای امنیت بیشتر، چند دقیقه صبر کن و دوباره تلاش کن.');

            return;
        }

        $credentials = [
            'email' => trim($validated['email']),
            'password' => $validated['password'],
        ];

        if (! Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($key, 60 * 5);

            $this->addError('email', 'ایمیل یا رمز عبور درست نیست.');

            return;
        }

        RateLimiter::clear($key);

        request()->session()->regenerate();

        $user = Auth::user();

        if (
            $user &&
            method_exists($user, 'isAdmin') &&
            $user->isAdmin()
        ) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended(route('dashboard'));
    }
};

?>

@php
    $siteName = setting('site.name', 'رندیکس');
@endphp

<div>
    <section class="pt-36 pb-24 px-5 md:px-10 min-h-screen">
        <div class="max-w-lg mx-auto">

            <div class="text-center space-y-5 mb-10">
                <div class="badge inline-flex">
                    <span class="badge-dot"></span>
                    ورود به {{ $siteName }}
                </div>

                <h1 class="text-4xl md:text-5xl font-black leading-[1.25]">
                    خوش برگشتی
                    <span class="gradient-text">👋</span>
                </h1>

                <p class="text-snow/60 leading-8">
                    وارد حساب کاربری خود شو تا درخواست‌ها، کدهای پیگیری و وضعیت پروژه‌هایت را ببینی.
                </p>
            </div>

            <form wire:submit="login" class="glass p-7 md:p-9 space-y-6">
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
                    <label class="text-sm font-bold text-snow/70">رمز عبور *</label>
                    <input
                        type="password"
                        wire:model="password"
                        placeholder="••••••••"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                        required
                    >
                    @error('password') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </div>

                <label class="flex items-center gap-3 text-sm text-snow/65 cursor-pointer">
                    <input type="checkbox" wire:model="remember" class="rounded border-white/20 bg-white/5 text-sky focus:ring-sky/30">
                    <span>مرا به خاطر بسپار</span>
                </label>

                <button type="submit" class="btn btn-primary w-full !py-4" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        ورود به حساب
                        <i class="fa-solid fa-right-to-bracket"></i>
                    </span>
                    <span wire:loading>در حال ورود...</span>
                </button>

                <p class="text-sm text-snow/55 text-center leading-7">
                    حساب نداری؟
                    <a href="{{ route('register') }}" class="text-sky font-bold hover:text-sky/80 transition-colors">
                        ثبت‌نام کن
                    </a>
                </p>
            </form>
        </div>
    </section>
</div>