<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $email = '';

    public bool $sent = false;

    public function sendLink()
    {
        $this->validate([
            'email' => ['required', 'email', 'max:190'],
        ], [
            'email.required' => 'ایمیل را وارد کنید.',
            'email.email' => 'ایمیل معتبر نیست.',
        ]);

        $status = Password::sendResetLink([
            'email' => trim($this->email),
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->sent = true;

            return;
        }

        $this->addError('email', __($status));
    }
};

?>

@php
    $siteName = setting('site.name', 'رندیکس');
    $pageTitle = 'فراموشی رمز عبور | ' . $siteName;
    $pageDescription = 'اگر رمز عبور خود را فراموش کرده‌اید، ایمیل خود را وارد کنید تا لینک بازیابی رمز برایتان ارسال شود.';
    $canonical = route('password.request');
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
                    بازیابی حساب کاربری
                </div>

                <h1 class="text-4xl md:text-5xl font-black leading-[1.25]">
                    رمز عبور را
                    <span class="gradient-text">فراموش کرده‌ای؟</span>
                </h1>

                <p class="text-snow/60 leading-8">
                    ایمیل خود را وارد کن. اگر حسابی با این ایمیل وجود داشته باشد، لینک بازنشانی رمز برایتان ارسال می‌شود.
                </p>
            </div>

            @if ($sent)
                <div class="glass p-7 md:p-9 space-y-6 text-center">
                    <div class="text-5xl text-emerald-300">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>

                    <h2 class="text-2xl font-black">
                        لینک بازیابی ارسال شد
                    </h2>

                    <p class="text-snow/65 leading-8">
                        اگر ایمیل شما در سیستم ثبت شده باشد، لینک بازنشانی رمز عبور ارسال شده است. پوشه اسپم را هم بررسی کن.
                    </p>

                    <form method="POST" action="{{ route('verification.send') }}" class="hidden">
                        @csrf
                    </form>

                    <div class="flex flex-wrap justify-center gap-3">
                        <button type="button" wire:click="$set('sent', false)" class="btn btn-primary">
                            ارسال دوباره
                        </button>

                        <a href="{{ route('login') }}" class="btn btn-primary">
                            بازگشت به ورود
                        </a>
                    </div>
                </div>
            @else
                <form wire:submit="sendLink" class="glass p-7 md:p-9 space-y-6">
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

                    <button type="submit" class="btn btn-primary w-full !py-4" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            ارسال لینک بازیابی
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <span wire:loading>در حال ارسال...</span>
                    </button>

                    <p class="text-sm text-snow/55 text-center leading-7">
                        یادت آمد؟
                        <a href="{{ route('login') }}" class="text-sky font-bold hover:text-sky/80 transition-colors">
                            وارد شو
                        </a>
                    </p>
                </form>
            @endif
        </div>
    </section>
</div>