<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow,noarchive">
    <title>{{ $title ?? 'پنل مدیریت رندیکس' }}</title>

    <link rel="icon" type="image/png" href="/images/favicon.png?v=6">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    @livewireStyles
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>

<body class="admin-body">
    <div class="admin-nebula" aria-hidden="true"></div>
    <div class="admin-stars" aria-hidden="true"></div>

    <div class="admin-shell">
        <x-admin.sidebar />

        <div class="admin-main">
            <x-admin.topbar />

            <main class="admin-content">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>

</html>