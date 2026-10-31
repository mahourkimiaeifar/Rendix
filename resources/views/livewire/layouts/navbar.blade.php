@php
    $siteName = setting('site.name', 'رندیکس');
    $logo = setting_url('brand.logo', asset('images/favicon.png?v=6'));

    $navItems = [
        ['route' => 'home', 'label' => 'خانه'],
        ['route' => 'about', 'label' => 'درباره ما'],
        ['route' => 'services', 'label' => 'خدمات'],
        ['route' => 'projects', 'label' => 'نمونه‌کارها'],
        ['route' => 'blog', 'label' => 'بلاگ'],
        ['route' => 'templates.index', 'label' => 'فروشگاه قالب'],
        ['route' => 'request-project', 'label' => 'درخواست پروژه'],
        ['route' => 'contact', 'label' => 'تماس با ما'],
    ];

    $visibleNavItems = [];

    foreach ($navItems as $item) {
        if (Route::has($item['route'])) {
            $visibleNavItems[] = $item;
        }
    }

    $isAdmin = auth()->check()
        && method_exists(auth()->user(), 'isAdmin')
        && auth()->user()->isAdmin();
@endphp

<header
    id="navbar"
    class="rx-nav-header"
    x-data="{ mobileOpen: false }"
    x-on:keydown.escape.window="mobileOpen = false"
>
    <div class="rx-nav-inner">
        <a href="{{ Route::has('home') ? route('home') : url('/') }}" class="rx-nav-brand" wire:navigate>
            <img src="{{ $logo }}" alt="{{ $siteName }}">
            <span>{{ $siteName }}</span>
        </a>

        <nav class="rx-nav-links" aria-label="ناوبری اصلی">
            @foreach ($visibleNavItems as $item)
                @php
                    $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
                @endphp

                <a
                    href="{{ route($item['route']) }}"
                    wire:navigate
                    class="rx-nav-link {{ $isActive ? 'is-active' : '' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="rx-nav-actions">
            <button
                type="button"
                class="rx-nav-toggle"
                x-on:click="mobileOpen = !mobileOpen"
                x-bind:aria-expanded="mobileOpen ? 'true' : 'false'"
                aria-controls="rx-mobile-nav"
                aria-label="باز و بسته کردن منو"
            >
                <i class="fa-solid fa-bars" x-show="!mobileOpen"></i>
                <i class="fa-solid fa-xmark" x-show="mobileOpen" x-cloak></i>
            </button>

            <button type="button" class="theme-toggle" onclick="rxToggleTheme()" aria-label="تغییر تم">
                <i class="fa-solid fa-moon"></i>
                <i class="fa-solid fa-sun"></i>
            </button>

            @auth
                @if (Route::has('dashboard'))
                    <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm rx-hide-mobile" wire:navigate>
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>داشبورد</span>
                    </a>
                @endif

                @if (Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}" class="rx-hide-mobile" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>خروج</span>
                        </button>
                    </form>
                @endif
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm rx-hide-mobile" wire:navigate>
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>ورود</span>
                    </a>
                @endif

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm" wire:navigate>
                        <i class="fa-solid fa-user-plus"></i>
                        <span>ثبت‌نام</span>
                    </a>
                @endif
            @endauth

            @if ($isAdmin && Route::has('admin.dashboard'))
                <a href="{{ route('admin.dashboard') }}" class="rx-nav-admin-link" title="پنل مدیریت">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>پنل مدیریت</span>
                </a>
            @endif
        </div>
    </div>

    <div
        id="rx-mobile-nav"
        class="rx-mobile-panel"
        x-show="mobileOpen"
        x-cloak
        x-transition.opacity.duration.180ms
    >
        <div class="rx-mobile-inner">
            @foreach ($visibleNavItems as $item)
                @php
                    $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
                @endphp

                <a
                    href="{{ route($item['route']) }}"
                    wire:navigate
                    class="rx-mobile-link {{ $isActive ? 'is-active' : '' }}"
                    x-on:click="mobileOpen = false"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach

            @auth
                @if (Route::has('dashboard'))
                    <a href="{{ route('dashboard') }}" class="rx-mobile-link" wire:navigate x-on:click="mobileOpen = false">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>داشبورد من</span>
                    </a>
                @endif

                @if (Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rx-mobile-link rx-mobile-form-button">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>خروج از حساب</span>
                        </button>
                    </form>
                @endif
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="rx-mobile-link" wire:navigate x-on:click="mobileOpen = false">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>ورود</span>
                    </a>
                @endif

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="rx-mobile-link" wire:navigate x-on:click="mobileOpen = false">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>ثبت‌نام</span>
                    </a>
                @endif
            @endauth

            @if ($isAdmin && Route::has('admin.dashboard'))
                <a href="{{ route('admin.dashboard') }}" class="rx-mobile-link" x-on:click="mobileOpen = false">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>پنل مدیریت</span>
                </a>
            @endif
        </div>
    </div>
</header>