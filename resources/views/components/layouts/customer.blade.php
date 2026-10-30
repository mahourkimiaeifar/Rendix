@php
    $siteName = setting('site.name', 'رندیکس');
    $favicon = setting_url('brand.favicon', asset('images/favicon.png?v=6'));

    $defaultTitle = $siteName . ' — ناحیه مشتری';
    $defaultDescription = setting(
        'site.tagline',
        'مدیریت درخواست‌های پروژه، کدهای پیگیری و وضعیت همکاری‌ها در ناحیه مشتری رندیکس.'
    );
@endphp
<!doctype html>
<html lang="fa" dir="rtl" class="dark" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('rendix.title', $defaultTitle)</title>

    <meta name="description" content="@yield('rendix.description', $defaultDescription)">
    <link rel="canonical" href="@yield('rendix.canonical', url()->current())">

    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="@yield('rendix.title', $defaultTitle)">
    <meta property="og:description" content="@yield('rendix.description', $defaultDescription)">
    <meta property="og:url" content="@yield('rendix.canonical', url()->current())">
    <meta property="og:image" content="@yield('rendix.image', setting_url('brand.og_image', $favicon))">
    <meta property="og:locale" content="fa_IR">
    <meta name="color-scheme" content="dark light">

    <link rel="icon" type="image/png" href="{{ $favicon }}">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">

    <script>
        (function () {
            try {
                var key = 'rendix-theme';
                var theme = null;

                try {
                    var stored = localStorage.getItem(key);
                    if (stored === 'light' || stored === 'dark') { theme = stored; }
                } catch (e) {}

                if (!theme) {
                    var cookie = document.cookie.split('; ').find(function (row) {
                        return row.indexOf(key + '=') === 0;
                    });
                    if (cookie) { theme = decodeURIComponent(cookie.split('=')[1]); }
                }

                if (theme !== 'light' && theme !== 'dark') { theme = 'dark'; }

                var root = document.documentElement;
                root.classList.toggle('light', theme === 'light');
                root.classList.toggle('dark', theme !== 'light');
                root.setAttribute('data-theme', theme);
            } catch (error) {}
        })();
    </script>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])

    @stack('rendix.head')
</head>
<body>
    <div id="rx-background" aria-hidden="true"></div>
    <div id="background" aria-hidden="true"></div>

    @include('livewire.layouts.navbar')

    <div class="rx-cust-container">
        @include('components.customer.sidebar')

        <main class="rx-cust-main">
            {{ $slot }}
        </main>
    </div>

    @include('livewire.layouts.footer')

    <div id="rx-scroll-progress" aria-hidden="true"></div>

    <button id="rx-back-top" type="button" aria-label="بازگشت به بالا">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <div id="rxds-toast-stack" class="rxds-toast-stack" aria-live="polite" aria-atomic="true"></div>

    @livewireScripts

    <script>
        (function () {
            if (!window.rxToggleTheme) {
                window.rxToggleTheme = function () {
                    try {
                        var root = document.documentElement;
                        var current = root.getAttribute('data-theme') || (root.classList.contains('light') ? 'light' : 'dark');
                        var next = current === 'light' ? 'dark' : 'light';

                        root.classList.toggle('light', next === 'light');
                        root.classList.toggle('dark', next !== 'light');
                        root.setAttribute('data-theme', next);

                        localStorage.setItem('rendix-theme', next);
                    } catch (e) {}
                };
            }

            function onScroll() {
                var navbar = document.getElementById('navbar');
                var progress = document.getElementById('rx-scroll-progress');
                var backTop = document.getElementById('rx-back-top');
                var y = window.scrollY || document.documentElement.scrollTop || 0;

                if (navbar) {
                    navbar.classList.toggle('nav-scrolled', y > 12);
                    navbar.classList.toggle('is-scrolled', y > 12);
                }

                if (progress) {
                    var h = document.documentElement.scrollHeight - window.innerHeight;
                    progress.style.width = h > 0 ? ((y / h) * 100).toFixed(2) + '%' : '0%';
                }

                if (backTop) {
                    backTop.classList.toggle('show', y > 420);
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                onScroll();

                var backTop = document.getElementById('rx-back-top');
                if (backTop) {
                    backTop.addEventListener('click', function () {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                }
            });

            document.addEventListener('livewire:navigated', onScroll);
            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', onScroll);
            window.addEventListener('load', onScroll);
        })();
    </script>

    @include('components.rx-toast-js')

    @stack('rendix.scripts')
</body>
</html>