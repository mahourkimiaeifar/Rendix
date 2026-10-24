@php
    $path = trim(request()->path(), '/');

    if ($path === '' || request()->routeIs('home')) {
        $page = 'home';
    } elseif (str_contains($path, 'about') || request()->routeIs('about')) {
        $page = 'about';
    } elseif (str_contains($path, 'contact') || request()->routeIs('contact')) {
        $page = 'contact';
    } else {
        $page = 'home';
    }

    $siteName = 'رندیکس';
    $siteNameLatin = 'Rendix';
    $baseUrl = url('/');
    $canonical = url()->current();
    $favicon = asset('/images/favicon.png');

    $pages = [
        'home' => [
            'title' => 'رندیکس | دیجیتال مارکتینگ در نجف‌آباد، طراحی وبسایت و سئو',
            'description' => 'رندیکس سیستم رشد دیجیتال کسب‌وکار شما را می‌سازد. دیجیتال مارکتینگ، طراحی وبسایت اختصاصی، سئو، شبکه‌های اجتماعی و فروشگاه اینترنتی در نجف‌آباد و سراسر ایران.',
            'keywords' => 'دیجیتال مارکتینگ نجف آباد, طراحی وبسایت نجف آباد, سئو نجف آباد, آژانس دیجیتال مارکتینگ, فروشگاه اینترنتی نجف آباد, مدیریت شبکه‌های اجتماعی, رندیکس',
        ],
        'about' => [
            'title' => 'درباره رندیکس | استودیوی فنی-خلاق با ریشه در نجف‌آباد',
            'description' => 'رندیکس یک استودیوی فنی-خلاق با تمرکز اولیه بر نجف‌آباد و خدمات برای سراسر ایران است. ما کیفیت را از روز اول جدی می‌گیریم و سیستم رشد دیجیتال می‌سازیم.',
            'keywords' => 'درباره رندیکس, استودیو دیجیتال مارکتینگ, نجف آباد, طراحی وبسایت اصفهان, سئو نجف آباد, رندیکس',
        ],
        'contact' => [
            'title' => 'تماس با رندیکس | مشاوره رایگان دیجیتال مارکتینگ',
            'description' => 'برای مشاوره رایگان دیجیتال مارکتینگ، طراحی وبسایت، سئو، شبکه‌های اجتماعی و فروشگاه اینترنتی با رندیکس در تماس باشید. ظرف ۲۴ ساعت پاسخ می‌دهیم.',
            'keywords' => 'تماس با رندیکس, مشاوره رایگان, دیجیتال مارکتینگ نجف آباد, طراحی وبسایت نجف آباد, سئو نجف آباد, رندیکس',
        ],
    ];

    $seo = $pages[$page] ?? $pages['home'];

    $schemaGraph = [
        [
            '@type' => 'Organization',
            '@id' => $baseUrl . '#organization',
            'name' => $siteName,
            'alternateName' => $siteNameLatin,
            'url' => $baseUrl,
            'logo' => $favicon,
            'image' => $favicon,
            'description' => $seo['description'],
            'areaServed' => [
                ['@type' => 'City', 'name' => 'نجف آباد'],
                ['@type' => 'AdministrativeArea', 'name' => 'اصفهان'],
                ['@type' => 'Country', 'name' => 'Iran'],
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'email' => 'info@rendix.ir',
                'contactType' => 'customer service',
                'availableLanguage' => ['Persian', 'Farsi'],
            ],
        ],
        [
            '@type' => 'WebSite',
            '@id' => $baseUrl . '#website',
            'url' => $baseUrl,
            'name' => $siteName,
            'alternateName' => $siteNameLatin,
            'inLanguage' => 'fa',
            'publisher' => [
                '@id' => $baseUrl . '#organization',
            ],
        ],
    ];

    if ($page === 'home') {
        $schemaGraph[] = [
            '@type' => 'ProfessionalService',
            '@id' => $baseUrl . '#professionalservice',
            'name' => $siteName,
            'url' => $baseUrl,
            'image' => $favicon,
            'description' => $seo['description'],
            'areaServed' => [
                ['@type' => 'City', 'name' => 'نجف آباد'],
                ['@type' => 'AdministrativeArea', 'name' => 'اصفهان'],
                ['@type' => 'Country', 'name' => 'Iran'],
            ],
            'provider' => [
                '@id' => $baseUrl . '#organization',
            ],
            'makesOffer' => [
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'دیجیتال مارکتینگ']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'طراحی وبسایت اختصاصی']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'سئو و بهینه‌سازی موتور جستجو']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'مدیریت شبکه‌های اجتماعی']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'فروشگاه اینترنتی']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'پشتیبانی و بهینه‌سازی سایت']],
            ],
        ];

        $schemaGraph[] = [
            '@type' => 'WebPage',
            '@id' => $canonical . '#webpage',
            'url' => $canonical,
            'name' => $seo['title'],
            'description' => $seo['description'],
            'inLanguage' => 'fa',
            'isPartOf' => ['@id' => $baseUrl . '#website'],
            'about' => ['@id' => $baseUrl . '#organization'],
        ];
    }

    if ($page === 'about') {
        $schemaGraph[] = [
            '@type' => 'AboutPage',
            '@id' => $canonical . '#webpage',
            'url' => $canonical,
            'name' => $seo['title'],
            'description' => $seo['description'],
            'inLanguage' => 'fa',
            'isPartOf' => ['@id' => $baseUrl . '#website'],
            'about' => ['@id' => $baseUrl . '#organization'],
        ];
    }

    if ($page === 'contact') {
        $schemaGraph[] = [
            '@type' => 'ContactPage',
            '@id' => $canonical . '#webpage',
            'url' => $canonical,
            'name' => $seo['title'],
            'description' => $seo['description'],
            'inLanguage' => 'fa',
            'isPartOf' => ['@id' => $baseUrl . '#website'],
            'mainEntity' => ['@id' => $baseUrl . '#organization'],
        ];
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => $schemaGraph,
    ];

    $schemaJson = json_encode(
        $schema,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    ) ?: '{}';
@endphp

@php
    $theme = request()->cookie('rendix-theme');

    if (!in_array($theme, ['light', 'dark'], true)) {
        $theme = 'dark';
    }
@endphp

<!DOCTYPE html>
<html lang="fa" dir="rtl" class="{{ $theme }}" data-theme="{{ $theme }}">

<head>
    @php
        $siteName ??= setting('site.name', 'رندیکس');

        $seo ??= [];

        $seo['title'] ??= isset($title) && $title
            ? $title . ' | ' . $siteName
            : $siteName . ' — طراحی وبسایت، سئو و دیجیتال مارکتینگ';

        $seo['description'] ??= setting(
            'site.tagline',
            'رندیکس؛ طراحی وبسایت، سئو و دیجیتال مارکتینگ با تمرکز بر رشد واقعی کسب‌وکار.'
        );

        $seo['keywords'] ??= setting('seo.keywords', '');

        $canonical ??= url()->current();

        $favicon ??= setting_url('brand.favicon', asset('images/favicon.png?v=6'));
        $ogImage = setting_url('brand.og_image', $favicon);
    @endphp

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('rendix.title', $seo['title'])</title>

    <meta name="description" content="@yield('rendix.description', $seo['description'])">
    <meta name="keywords" content="@yield('rendix.keywords', $seo['keywords'])">
    <meta name="robots" content="@yield('rendix.robots', 'index, follow')">

    <link rel="canonical" href="@yield('rendix.canonical', $canonical)">

    <meta property="og:type" content="@yield('rendix.type', 'website')">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="@yield('rendix.title', $seo['title'])">
    <meta property="og:description" content="@yield('rendix.description', $seo['description'])">
    <meta property="og:url" content="@yield('rendix.canonical', $canonical)">
    <meta property="og:image" content="@yield('rendix.image', $ogImage)">
    <meta property="og:image:alt" content="{{ $siteName }}">
    <meta property="og:locale" content="fa_IR">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('rendix.title', $seo['title'])">
    <meta name="twitter:description" content="@yield('rendix.description', $seo['description'])">
    <meta name="twitter:image" content="@yield('rendix.image', $ogImage)">
    <meta name="color-scheme" content="dark light">

    <link rel="icon" type="image/png" href="{{ $favicon }}">
    <link rel="apple-touch-icon" href="{{ $favicon }}">

    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">

    <script>
        (function () {
            try {
                var key = 'rendix-theme';
                var theme = null;

                try {
                    var stored = localStorage.getItem(key);
                    if (stored === 'light' || stored === 'dark') {
                        theme = stored;
                    }
                } catch (e) { }

                if (!theme) {
                    var cookie = document.cookie
                        .split('; ')
                        .find(function (row) {
                            return row.indexOf(key + '=') === 0;
                        });

                    if (cookie) {
                        var value = cookie.split('=')[1];
                        theme = decodeURIComponent(value);
                    }
                }

                if (theme !== 'light' && theme !== 'dark') {
                    theme = 'dark';
                }

                var root = document.documentElement;

                root.classList.toggle('light', theme === 'light');
                root.classList.toggle('dark', theme !== 'light');
                root.setAttribute('data-theme', theme);

                document.cookie = key + '=' + theme + '; path=/; max-age=31536000; SameSite=Lax';
            } catch (error) {
                document.documentElement.classList.add('dark');
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])

    @stack('rendix.head')
</head>

<body class="antialiased">

    <div id="rx-scroll-progress"></div>

    <div id="rendix-canvas" class="fixed inset-0 -z-10 pointer-events-none" aria-hidden="true"></div>

    <livewire:layouts.navbar />

    <main class="relative">
        {{ $slot }}
    </main>

    <x-site.footer />

    <button id="rx-back-top" type="button" aria-label="بازگشت به بالا">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <script type="application/ld+json">{!! $schemaJson !!}</script>

    @livewireScripts
</body>

</html>