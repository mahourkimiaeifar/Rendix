<!doctype html>
<html lang="fa" dir="rtl" class="dark" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>۵۰۰ — خطای سرور | رندیکس</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png?v=6') }}">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @vite(['resources/css/app.css'])
</head>
<body class="rx-error-body">
    <main class="rx-error-card">
        <div class="rx-error-code">۵۰۰</div>
        <h1>موتور کهکشان کمی داغ کرده</h1>
        <p>یک خطای سمت سرور رخ داد. تیم فنی رندیکس به‌صورت خودکار مطلع شد؛ لطفاً چند لحظه بعد دوباره سر بزن.</p>
        <div class="rx-error-actions">
            <button type="button" class="btn btn-primary" onclick="location.reload()">
                <i class="fa-solid fa-rotate"></i> تلاش دوباره
            </button>
            <a href="{{ url('/contact') }}" class="btn btn-ghost"><i class="fa-solid fa-paper-plane"></i> اطلاع به ما</a>
        </div>
    </main>
</body>
</html>