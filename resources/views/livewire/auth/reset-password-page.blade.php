<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    #[Url]
    public string $email = '';

    public string $token = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = trim($token);
        $this->email = trim((string) request('email', ''));
    }

    public function resetPassword()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ], [
            'email.required' => 'ایمیل الزامی است.',
            'email.email' => 'ایمیل معتبر نیست.',
            'password.required' => 'رمز عبور جدید را وارد کنید.',
            'password.confirmed' => 'تکرار رمز عبور مطابق نیست.',
            'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
        ]);

        $status = Password::reset(
            [
                'email' => trim($this->email),
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                Auth::login($user);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('dashboard');
        }

        $this->addError('email', __($status));
    }
};

?>

@php
    $siteName = setting('site.name', 'رندیکس');
    $pageTitle = 'بازنشانی رمز عبور | ' . $siteName;
    $pageDescription = 'رمز عبور جدید خود را وارد کنید تا دسترسی حساب کاربری‌تان بازیابی شود.';
    $canonical = route('password.reset', ['token' => $token]);
@endphp

@section('rendix.title', $pageTitle)
@section('rendix.description', $pageDescription)
@section('rendix.canonical', $canonical)
@section('rendix.type', 'WebPage')

<div>
    <section class="pt-36 pb-24 px-5 md:px-10 min-h-screen">
        <div class="max-w-lg mx-auto">

            <div class="text-center space-y-5 mb-10">
                <div class="badge inline-flex">
                    <span class="badge-dot"></span>
                    تنظیم رمز جدید
                </div>

                <h1 class="text-4xl md:text-5xl font-black leading-[1.25]">
                    رمز عبور جدید را
                    <span class="gradient-text">انتخاب کن</span>
                </h1>

                <p class="text-snow/60 leading-8">
                    ایمیل و رمز جدید خود را وارد کن. بعد از موفقیت، وارد داشبورد می‌شوی.
                </p>
            </div>

            <form wire:submit="resetPassword" class="glass p-7 md:p-9 space-y-6">
                <input type="hidden" wire:model="token">

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
                    <label class="text-sm font-bold text-snow/70">رمز عبور جدید *</label>
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
                    <label class="text-sm font-bold text-snow/70">تکرار رمز عبور جدید *</label>
                    <input
                        type="password"
                        wire:model="password_confirmation"
                        placeholder="تکرار رمز عبور"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary w-full !py-4" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        بازنشانی رمز عبور
                        <i class="fa-solid fa-shield-halved"></i>
                    </span>
                    <span wire:loading>در حال ذخیره...</span>
                </button>

                <p class="text-sm text-snow/55 text-center leading-7">
                    <a href="{{ route('login') }}" class="text-sky font-bold hover:text-sky/80 transition-colors">
                        بازگشت به ورود
                    </a>
                </p>
            </form>
        </div>
    </section>
</div>