@props([
    'location' => 'all',
    'title' => 'سوالات متداول',
    'subtitle' => 'پاسخ رایج‌ترین سوال‌های مشتریان رندیکس.',
])

@php
    $faqs = \App\Models\Faq::query()
        ->active()
        ->forLocation($location)
        ->ordered()
        ->get();
@endphp

@if ($faqs->isNotEmpty())
    <section class="rx-faq-section">
        <div class="rx-container">
            <div class="rx-section-head">
                <h2>{{ $title }}</h2>
                <p>{{ $subtitle }}</p>
            </div>

            <div class="rx-faq-list">
                @foreach ($faqs as $faq)
                    <details class="rx-faq-item">
                        <summary>
                            <span>{{ $faq->question }}</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </summary>

                        <div class="rx-faq-answer">
                            {{ $faq->answer }}
                        </div>
                    </details>
                @endforeach
            </div>
        </div>
    </section>
@endif