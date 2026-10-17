<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به پنل رندیکس</title>

    <link rel="icon" type="image/png" href="/images/favicon.png?v=6">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">

    @livewireStyles
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="admin-auth-body">
    <div class="admin-auth-shell">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>