@props([
    'class' => '',
])

@if (
    auth()->check() &&
    method_exists(auth()->user(), 'isAdmin') &&
    auth()->user()->isAdmin()
)
    <a
        href="{{ route('admin.dashboard') }}"
        class="rx-nav-admin-link {{ $class }}"
        title="ورود به پنل مدیریت رندیکس"
    >
        <i class="fa-solid fa-gauge-high"></i>
        <span>پنل مدیریت</span>
    </a>
@endif