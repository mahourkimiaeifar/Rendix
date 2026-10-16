<!DOCTYPE html>
<html lang="fa" dir="rtl" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <script>
        (function () {
            var d = document.documentElement;
            d.classList.add('rx-js');

            window.RendixTheme = {
                get: function () {
                    try {
                        return localStorage.getItem('rendix-theme') || 'dark';
                    } catch (e) {
                        return 'dark';
                    }
                },
                apply: function (theme) {
                    var isLight = theme === 'light';
                    d.classList.toggle('light', isLight);
                    d.setAttribute('data-theme', theme);

                    window.dispatchEvent(
                        new CustomEvent('rendix-theme-changed', {
                            detail: { isLight: isLight }
                        })
                    );
                },
                set: function (theme) {
                    try {
                        localStorage.setItem('rendix-theme', theme);
                    } catch (e) { }

                    this.apply(theme);
                },
                toggle: function () {
                    this.set(this.get() === 'light' ? 'dark' : 'light');
                }
            };

            window.RendixTheme.apply(window.RendixTheme.get());
        })();
    </script>

    <x-seo :title="$title ?? 'رندیکس | دیجیتال مارکتینگ، طراحی وبسایت و سئو'" :description="$metaDescription ?? 'رندیکس؛ استودیوی فنی-خلاق برای دیجیتال مارکتینگ، طراحی وبسایت اختصاصی، سئو، شبکه‌های اجتماعی و فروشگاه اینترنتی.'"
        :keywords="$keywords ?? ''" :image="$ogImage ?? '/images/og-image.jpg'" :schema="$schema ?? []" />

    <link rel="icon" type="image/png" href="/images/favicon.png?v=5">
    <link rel="apple-touch-icon" href="/images/logo.png?v=5">

    @vite(['resources/css/app.css', 'resources/js/app.jsx'])

    @livewireStyles
</head>

<body class="antialiased">

    <div class="rx-ambient" aria-hidden="true"></div>

    @persist('three-canvas')
    <div id="rendix-canvas-container" class="rx-canvas-layer" aria-hidden="true"></div>
    @endpersist

    <div id="rx-scroll-progress" class="rx-scroll-progress"></div>

    <div id="rendix-preloader" class="rx-preloader">
        <div class="rx-preloader-inner">
            <div class="rx-loader-logo">
                <img src="/images/logo.png?v=5" alt="رندیکس">
            </div>

            <div class="rx-loader-stage">
                <div class="rx-loader-ring-spin" aria-hidden="true"></div>

                <div class="rx-loader-percent">
                    <span id="countdown">۰</span><span class="rx-loader-sign">٪</span>
                </div>
            </div>

            <div class="rx-loader-bar">
                <div id="progress-bar" class="rx-loader-bar-inner"></div>
            </div>

            <div class="rx-loader-status">
                <span class="rx-status-prefix">></span>
                <span id="loader-status">initializing_rendix</span>
                <span class="rx-cursor">_</span>
            </div>
        </div>
    </div>

    <livewire:navbar />

    <main id="rx-main" class="rx-main">
        {{ $slot }}
    </main>

    <livewire:footer />

    <button id="rx-back-to-top" type="button" class="rx-back-to-top" aria-label="بازگشت به بالای صفحه">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    @livewireScripts

    @stack('scripts')
</body>

</html>