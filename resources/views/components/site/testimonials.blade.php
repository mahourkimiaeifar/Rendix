@props([
    'limit' => 6,
    'title' => 'مشتری‌ها چه می‌گویند؟',
    'subtitle' => 'بازخورد واقعی کسانی که با رندیکس رشد کرده‌اند.',
])

@php
    $testimonials = \App\Models\Testimonial::query()
        ->active()
        ->ordered()
        ->limit($limit)
        ->get();
@endphp

@if ($testimonials->isNotEmpty())
    <section class="rx-testimonials-section">
        <div class="rx-container">
            <div class="rx-section-head">
                <h2>{{ $title }}</h2>
                <p>{{ $subtitle }}</p>
            </div>

            <div class="rx-testimonials-grid">
                @foreach ($testimonials as $testimonial)
                    <article class="rx-testimonial-card">
                        <div class="rx-testimonial-stars">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star {{ $i <= $testimonial->rating ? 'active' : '' }}"></i>
                            @endfor
                        </div>

                        <p class="rx-testimonial-content">
                            {{ $testimonial->content }}
                        </p>

                        <div class="rx-testimonial-user">
                            @if ($testimonial->avatar_path)
                                <img src="{{ $testimonial->avatarUrl() }}" alt="{{ $testimonial->name }}">
                            @else
                                <span class="rx-testimonial-avatar-placeholder">
                                    {{ $testimonial->initials() }}
                                </span>
                            @endif

                            <div>
                                <strong>{{ $testimonial->name }}</strong>
                                <small>
                                    {{ collect([$testimonial->role, $testimonial->company])->filter()->implode(' — ') ?: 'مشتری رندیکس' }}
                                </small>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif