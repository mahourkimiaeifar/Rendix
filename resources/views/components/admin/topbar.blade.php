@php
    $user = auth()->user();
    $siteName = setting('site.name', 'رندیکس');

    $unreadNotifications = 0;

    try {
        if ($user && method_exists($user, 'unreadNotifications')) {
            $unreadNotifications = $user->unreadNotifications()->count();
        }
    } catch (\Throwable $e) {
        $unreadNotifications = 0;
    }

    $nowDate = '';

    try {
        $nowDate = \App\Support\Jalali::date(now())
            . ' — ساعت '
            . \App\Support\Jalali::toPersianDigits(now()->format('H:i'));
    } catch (\Throwable $e) {
        $nowDate = now()->format('Y/m/d H:i');
    }

    $routeName = (string) (request()->route()?->getName() ?? '');

    $breadcrumbMap = [
        'admin.dashboard' => 'داشبورد',
        'admin.leads' => 'سرنخ‌ها',
        'admin.project-requests' => 'درخواست‌های پروژه',
        'admin.posts' => 'مقالات',
        'admin.categories' => 'دسته‌بندی‌ها',
        'admin.tags' => 'تگ‌ها',
        'admin.pages' => 'صفحات اختصاصی',
        'admin.services' => 'خدمات',
        'admin.projects' => 'نمونه‌کارها',
        'admin.testimonials' => 'نظرات مشتریان',
        'admin.faqs' => 'سوالات متداول',
        'admin.settings' => 'تنظیمات سایت',
        'admin.profile' => 'پروفایل',
        'admin.diagnostics' => 'تشخیص سایت',
    ];

    $breadcrumb = 'پنل مدیریت';

    foreach ($breadcrumbMap as $prefix => $label) {
        if ($routeName === $prefix || str_starts_with($routeName, $prefix . '.')) {
            $breadcrumb = $label;
            break;
        }
    }
@endphp

<header class="admin-topbar">
    <div class="admin-topbar-start">
        <button
            type="button"
            class="admin-icon-btn mobile-only"
            onclick="document.body.classList.toggle('admin-sidebar-open')"
            aria-label="باز و بسته کردن سایدبار"
        >
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="admin-breadcrumb">
            <i class="fa-solid fa-house"></i>
            <span>{{ $siteName }}</span>
            <i class="fa-solid fa-chevron-left"></i>
            <strong>{{ $breadcrumb }}</strong>
        </div>

        <div class="admin-now">
            <i class="fa-solid fa-star"></i>
            <span>{{ $nowDate }}</span>
        </div>
    </div>

    <div class="admin-topbar-end">
        @if (Route::has('home'))
            <a
                href="{{ route('home') }}"
                target="_blank"
                rel="noopener noreferrer"
                class="admin-btn admin-btn-ghost rx-open-site"
                title="باز کردن وبسایت اصلی در تب جدید"
            >
                <i class="fa-solid fa-globe"></i>
                <span>وبسایت</span>
            </a>
        @endif

        <livewire:admin.maintenance-toggle />

        <button type="button" class="admin-icon-btn" title="اعلان‌ها" style="position:relative;">
            <i class="fa-solid fa-bell"></i>

            @if ($unreadNotifications > 0)
                <span style="position:absolute;top:8px;inset-inline-end:8px;width:8px;height:8px;border-radius:999px;background:#ef4444;box-shadow:0 0 0 3px rgba(7,19,41,.9);"></span>
            @endif
        </button>

        <details class="admin-user-menu">
            <summary>
                <span class="admin-avatar small">
                    {{ mb_substr($user->name ?? 'R', 0, 1) }}
                </span>

                <span class="admin-user-info">
                    <strong>{{ $user->name ?? 'مدیر' }}</strong>
                    <small>{{ $user->email ?? '' }}</small>
                </span>

                <i class="fa-solid fa-chevron-down" style="font-size:11px;color:var(--rx-soft);"></i>
            </summary>

            <div class="admin-user-dropdown">
                @if (Route::has('admin.profile'))
                    <a href="{{ route('admin.profile') }}" class="admin-dropdown-item">
                        <i class="fa-solid fa-user-gear"></i>
                        <span>پروفایل</span>
                    </a>
                @endif

                @if (Route::has('admin.diagnostics'))
                    <a href="{{ route('admin.diagnostics') }}" class="admin-dropdown-item">
                        <i class="fa-solid fa-stethoscope"></i>
                        <span>تشخیص سایت</span>
                    </a>
                @endif

                @if (Route::has('home'))
                    <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="admin-dropdown-item">
                        <i class="fa-solid fa-globe"></i>
                        <span>مشاهده سایت</span>
                    </a>
                @endif

                <div style="height:1px;margin:6px 8px;background:var(--rx-border);"></div>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="admin-dropdown-item danger">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>خروج از پنل</span>
                    </button>
                </form>
            </div>
        </details>
    </div>
</header>