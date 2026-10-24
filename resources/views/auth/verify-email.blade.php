@extends('components.layouts.app')

@section('content')
    @php
        $siteName = setting('site.name', 'رندیکس');
        $user = auth()->user();
    @endphp

    <section class="pt-36 pb-24 px-5 md:px-10 min-h-screen">
        <div class="max-w-lg mx-auto">

            <div class="text-center space-y-5 mb-10">
                <div class="badge inline-flex">
                    <span class="badge-dot"></span>
                    تأیید ایمیل
                </div>

                <h1 class="text-4xl md:text-5xl font-black leading-[1.25]">
                    ایمیل خود را
                    <span class="gradient-text">تأیید کن</span>
                </h1>

                <p class="text-snow/60 leading-8">
                    برای امنیت حساب کاربری و دریافت اعلان‌های رسمی {{ $siteName }}، روی لینک ارسال‌شده در ایمیل بزن.
                </p>
            </div>

            @if (session('status') === 'verification-link-sent')
                <div class="glass p-5 mb-6 rounded-2xl border border-emerald-400/25 bg-emerald-400/10 text-emerald-200 text-sm leading-7 text-center">
                    <i class="fa-solid fa-circle-check"></i>
                    لینک تأیید دوباره ارسال شد.
                </div>
            @endif

            @if ($user && $user->hasVerifiedEmail())
                <div class="glass p-7 md:p-9 space-y-6 text-center">
                    <div class="text-5xl text-emerald-300">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <h2 class="text-2xl font-black">
                        ایمیل شما تأیید شده است
                    </h2>

                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        رفتن به داشبورد
                    </a>
                </div>
            @else
                <div class="glass p-7 md:p-9 space-y-6 text-center">
                    <p class="text-snow/65 leading-8">
                        اگر ایمیل تأیید را دریافت نکردی، می‌توانی دوباره درخواست بدهی.
                    </p>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf

                        <button type="submit" class="btn btn-primary w-full !py-4">
                            ارسال دوباره لینک تأیید
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="text-sm text-snow/55 hover:text-sky transition-colors bg-transparent border-0 p-0 cursor-pointer font-inherit">
                            خروج از حساب
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </section>
@endsection