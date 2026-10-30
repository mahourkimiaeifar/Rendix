@props([
    'badge' => null,
    'title' => null,
    'gradient' => null,
    'lede' => null,
])

<section class="rx-page-hero">
    <div class="rx-hero-glow" aria-hidden="true"></div>

    <div class="rx-container">
        @if ($badge)
            <div class="badge fade-up">
                <span class="badge-dot"></span>
                {{ $badge }}
            </div>
        @endif

        <h1 class="fade-up delay-1">
            {{ $title }}
            @if ($gradient)
                <br>
                <span class="gradient-text">{{ $gradient }}</span>
            @endif
        </h1>

        @if ($lede)
            <p class="lede fade-up delay-2">{{ $lede }}</p>
        @endif

        @if (isset($actions))
            <div class="rx-hero-actions fade-up delay-3">
                {{ $actions }}
            </div>
        @endif

        {{ $slot }}
    </div>
</section>