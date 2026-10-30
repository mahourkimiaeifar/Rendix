@props([
    'icon' => 'fa-solid fa-satellite-dish',
    'title' => 'چیزی پیدا نشد',
    'text' => 'فعلاً محتوایی در این بخش نیست؛ به‌زودی کامل می‌شود.',
])

<div class="rx-empty-state rv">
    <i class="{{ $icon }}"></i>
    <h2>{{ $title }}</h2>
    <p>{{ $text }}</p>
    {{ $slot }}
</div>