@php
    $siteName = setting('site.name', 'رندیکس');
    $logo = setting_url('brand.logo', asset('images/favicon.png?v=6'));

    $groups = [
        [
            'title' => 'مدیریت',
            'items' => [
                ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'label' => 'داشبورد', 'icon' => 'fa-solid fa-gauge-high'],
                ['route' => 'admin.leads.index', 'match' => 'admin.leads.*', 'label' => 'سرنخ‌ها', 'icon' => 'fa-solid fa-users'],
                ['route' => 'admin.project-requests.index', 'match' => 'admin.project-requests.*', 'label' => 'درخواست‌های پروژه', 'icon' => 'fa-solid fa-clipboard-list'],
            ],
        ],
        [
            'title' => 'محتوا و بلاگ',
            'items' => [
                ['route' => 'admin.posts.index', 'match' => 'admin.posts.*', 'label' => 'مقالات', 'icon' => 'fa-solid fa-newspaper'],
                ['route' => 'admin.categories', 'match' => 'admin.categories', 'label' => 'دسته‌بندی‌ها', 'icon' => 'fa-solid fa-folder-tree'],
                ['route' => 'admin.tags', 'match' => 'admin.tags', 'label' => 'تگ‌ها', 'icon' => 'fa-solid fa-tags'],
                ['route' => 'admin.pages', 'match' => 'admin.pages', 'label' => 'صفحات اختصاصی', 'icon' => 'fa-solid fa-file-lines'],
            ],
        ],
        [
            'title' => 'سایت',
            'items' => [
                ['route' => 'admin.services.index', 'match' => 'admin.services.*', 'label' => 'خدمات', 'icon' => 'fa-solid fa-layer-group'],
                ['route' => 'admin.projects.index', 'match' => 'admin.projects.*', 'label' => 'نمونه‌کارها', 'icon' => 'fa-solid fa-diagram-project'],
                ['route' => 'admin.testimonials', 'match' => 'admin.testimonials', 'label' => 'نظرات مشتریان', 'icon' => 'fa-solid fa-quote-right'],
                ['route' => 'admin.faqs', 'match' => 'admin.faqs', 'label' => 'سوالات متداول', 'icon' => 'fa-solid fa-circle-question'],
                ['route' => 'admin.settings', 'match' => 'admin.settings', 'label' => 'تنظیمات سایت', 'icon' => 'fa-solid fa-sliders'],
            ],
        ],
        [
            'title' => 'سیستم',
            'items' => [
                ['route' => 'admin.profile', 'match' => 'admin.profile', 'label' => 'پروفایل', 'icon' => 'fa-solid fa-user-gear'],
                ['route' => 'admin.diagnostics', 'match' => 'admin.diagnostics', 'label' => 'تشخیص سایت', 'icon' => 'fa-solid fa-stethoscope'],
            ],
        ],
    ];

    $maintenanceEnabled = false;

    try {
        $maintenanceEnabled = \App\Support\MaintenanceMode::enabled();
    } catch (\Throwable $e) {
        $maintenanceEnabled = false;
    }
@endphp

<aside class="admin-sidebar">
    <div class="admin-sidebar-brand">
        <img src="{{ $logo }}" alt="{{ $siteName }}">
        <div>
            <strong>{{ $siteName }}</strong>
            <span>پنل مدیریت</span>
        </div>
    </div>

    <nav class="admin-nav" aria-label="ناوبری پنل">
        @foreach ($groups as $group)
            <div class="admin-nav-group">
                <p class="admin-nav-title">{{ $group['title'] }}</p>

                @foreach ($group['items'] as $item)
                    @if (Route::has($item['route']))
                        <a
                            href="{{ route($item['route']) }}"
                            class="admin-nav-item {{ request()->routeIs($item['match']) ? 'active' : '' }}"
                        >
                            <i class="{{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        @endforeach
    </nav>

    <div class="admin-sidebar-footer">
        <div class="admin-mini-card">
            <i class="fa-solid fa-satellite-dish"></i>
            <div>
                <strong>وضعیت سایت</strong>
                <span>
                    @if ($maintenanceEnabled)
                        حالت تعمیرات فعال است
                    @else
                        سایت به‌صورت عادی در دسترس است
                    @endif
                </span>
            </div>
        </div>
    </div>
</aside>