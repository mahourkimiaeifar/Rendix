@props([
    'title' => null,
    'text' => null,
])

@php
    $title ??= 'آماده‌ای رشد دیجیتال را شروع کنی؟';
    $text ??= 'پروژه‌ات را مرحله‌به‌مرحله تعریف کن؛ ما بر اساس هدف، بودجه و زمان‌بندی، مسیر اجرا را پیشنهاد می‌دهیم.';
    $telegram = setting('social.telegram');
@endphp

<section class="rx-section tight">
    <div class="rx-container">
        <div class="rx-cta-band rv">
            <div>
                <h2>{{ $title }}</h2>
                <p>{{ $text }}</p>
            </div>

            <div class="rx-cta-actions">
                @if (Route::has('request-project'))
                    <a href="{{ route('request-project') }}" class="btn btn-primary" wire:navigate>
                        <i class="fa-solid fa-rocket"></i>
                        <span>درخواست پروژه</span>
                    </a>
                @endif

                @if (Route::has('contact'))
                    <a href="{{ route('contact') }}" class="btn btn-ghost" wire:navigate>
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>تماس با ما</span>
                    </a>
                @endif

                @if ($telegram)
                    <a href="{{ $telegram }}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost">
                        <i class="fa-brands fa-telegram"></i>
                        <span>تلگرام</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>