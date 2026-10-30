@php
    $siteName = setting('site.name', 'رندیکس');

    $adminVite = ['resources/css/admin.css'];

    if (file_exists(resource_path('js/admin.js'))) {
        $adminVite[] = 'resources/js/admin.js';
    }
@endphp
<!doctype html>
<html lang="fa" dir="rtl" class="dark" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', 'ورود به پنل ' . $siteName)</title>

    <link rel="icon" type="image/png" href="{{ setting_url('brand.favicon', asset('images/favicon.png?v=6')) }}">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">

    @livewireStyles
    @vite($adminVite)
</head>
<body class="admin-auth-body">
    {{ $slot }}

    <div id="rxds-toast-stack" class="rxds-toast-stack" aria-live="polite" aria-atomic="true"></div>

    @livewireScripts

    @include('components.rx-toast-js')
</body>
</html>