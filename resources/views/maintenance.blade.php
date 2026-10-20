<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow,noarchive">

    <title>رندیکس در حال تعمیرات کهکشانی است</title>

    <link rel="icon" type="image/png" href="/images/favicon.png?v=6">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Vazirmatn, system-ui, sans-serif;
            background:
                radial-gradient(circle at 18% 18%, rgba(69, 200, 255, 0.18), transparent 32%),
                radial-gradient(circle at 82% 12%, rgba(118, 83, 246, 0.18), transparent 34%),
                radial-gradient(circle at 50% 82%, rgba(23, 105, 255, 0.16), transparent 36%),
                linear-gradient(160deg, #050b18, #071329 45%, #030712);
            color: #F7F9FF;
            display: grid;
            place-items: center;
            overflow: hidden;
        }

        .rx-maintain-stars,
        .rx-maintain-stars::before,
        .rx-maintain-stars::after {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                radial-gradient(circle, rgba(255,255,255,.9) 1px, transparent 1px),
                radial-gradient(circle, rgba(255,255,255,.65) 1px, transparent 1px),
                radial-gradient(circle, rgba(255,255,255,.45) 1px, transparent 1px);
            background-size: 220px 220px, 160px 160px, 300px 300px;
            background-position: 0 0, 60px 40px, 120px 90px;
            animation: rxMaintainStars 80s linear infinite;
            opacity: .42;
        }

        .rx-maintain-stars::before {
            animation-duration: 120s;
            opacity: .28;
        }

        .rx-maintain-stars::after {
            animation-duration: 160s;
            opacity: .18;
        }

        .rx-maintain-shell {
            position: relative;
            z-index: 1;
            width: min(860px, calc(100% - 32px));
            padding: 34px;
            border-radius: 34px;
            background: linear-gradient(135deg, rgba(255,255,255,.08), rgba(255,255,255,.025));
            border: 1px solid rgba(255,255,255,.12);
            box-shadow:
                0 40px 120px -40px rgba(0,0,0,.92),
                inset 0 1px 0 rgba(255,255,255,.06);
            backdrop-filter: blur(24px) saturate(170%);
            -webkit-backdrop-filter: blur(24px) saturate(170%);
            text-align: center;
        }

        .rx-maintain-orbit {
            position: relative;
            width: 190px;
            height: 190px;
            margin: 0 auto 26px;
            display: grid;
            place-items: center;
        }

        .rx-maintain-planet {
            width: 92px;
            height: 92px;
            border-radius: 999px;
            background:
                radial-gradient(circle at 30% 25%, rgba(255,255,255,.55), transparent 26%),
                linear-gradient(145deg, #1769FF, #45C8FF 48%, #7653F6);
            box-shadow:
                0 0 0 1px rgba(255,255,255,.12),
                0 24px 70px -18px rgba(23,105,255,.95),
                inset 0 0 28px rgba(255,255,255,.16);
            animation: rxMaintainPulse 3.2s ease-in-out infinite;
        }

        .rx-maintain-ring {
            position: absolute;
            inset: 0;
            border-radius: 999px;
            border: 1px dashed rgba(69,200,255,.32);
            animation: rxMaintainSpin 12s linear infinite;
        }

        .rx-maintain-ring::before {
            content: '';
            position: absolute;
            top: -7px;
            right: 28px;
            width: 14px;
            height: 14px;
            border-radius: 999px;
            background: #45C8FF;
            box-shadow: 0 0 22px rgba(69,200,255,.95);
        }

        .rx-maintain-ring.two {
            inset: 24px;
            border-color: rgba(118,83,246,.28);
            animation-duration: 18s;
            animation-direction: reverse;
        }

        .rx-maintain-ring.two::before {
            background: #7653F6;
            box-shadow: 0 0 22px rgba(118,83,246,.95);
            right: auto;
            left: 18px;
            top: auto;
            bottom: -7px;
        }

        .rx-maintain-badge {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 9px 14px;
            border-radius: 999px;
            background: rgba(245,158,11,.10);
            border: 1px solid rgba(245,158,11,.22);
            color: #fed7aa;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 18px;
        }

        .rx-maintain-badge i {
            animation: rxMaintainWrench 2.2s ease-in-out infinite;
        }

        h1 {
            margin: 0;
            font-size: clamp(30px, 5vw, 52px);
            line-height: 1.25;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        h1 span {
            background: linear-gradient(90deg, #45C8FF, #1769FF 55%, #7653F6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .rx-maintain-text {
            margin: 18px auto 0;
            max-width: 620px;
            color: rgba(247,249,255,.74);
            font-size: 16px;
            line-height: 2.1;
        }

        .rx-maintain-actions {
            margin-top: 28px;
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .rx-maintain-btn {
            min-height: 50px;
            padding: 13px 22px;
            border-radius: 16px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 950;
            font-size: 15px;
            transition: all .24s ease;
        }

        .rx-maintain-btn-primary {
            color: #fff;
            background: linear-gradient(90deg, #1769FF, #45C8FF);
            box-shadow: 0 18px 42px -18px rgba(23,105,255,.95);
        }

        .rx-maintain-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 24px 54px -18px rgba(69,200,255,.95);
        }

        .rx-maintain-btn-ghost {
            color: rgba(247,249,255,.82);
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.10);
        }

        .rx-maintain-btn-ghost:hover {
            color: #fff;
            border-color: rgba(69,200,255,.32);
            background: rgba(69,200,255,.09);
        }

        .rx-maintain-footer {
            margin-top: 28px;
            color: rgba(247,249,255,.38);
            font-size: 12px;
            font-weight: 850;
        }

        @keyframes rxMaintainStars {
            from { transform: translate3d(0, 0, 0); }
            to { transform: translate3d(-220px, -220px, 0); }
        }

        @keyframes rxMaintainSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes rxMaintainPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.06); }
        }

        @keyframes rxMaintainWrench {
            0%, 100% { transform: rotate(-8deg); }
            50% { transform: rotate(12deg); }
        }

        @media (max-width: 640px) {
            .rx-maintain-shell {
                padding: 26px 20px;
                border-radius: 28px;
            }

            .rx-maintain-orbit {
                width: 160px;
                height: 160px;
            }

            .rx-maintain-planet {
                width: 76px;
                height: 76px;
            }

            .rx-maintain-text {
                font-size: 15px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .rx-maintain-stars,
            .rx-maintain-stars::before,
            .rx-maintain-stars::after,
            .rx-maintain-ring,
            .rx-maintain-planet,
            .rx-maintain-badge i {
                animation: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="rx-maintain-stars" aria-hidden="true"></div>

    <main class="rx-maintain-shell">
        <div class="rx-maintain-orbit" aria-hidden="true">
            <div class="rx-maintain-ring"></div>
            <div class="rx-maintain-ring two"></div>
            <div class="rx-maintain-planet"></div>
        </div>

        <div class="rx-maintain-badge">
            <i class="fa-solid fa-screwdriver-wrench"></i>
            <span>حالت تعمیرات فعال است</span>
        </div>

        <h1>
            کهکشان
            <span>رندیکس</span>
            در حال درخشش دوباره است
        </h1>

        <p class="rx-maintain-text">
            {{ $maintenance['message'] ?? 'ما داریم موتور رندیکس را کوک می‌کنیم؛ چند دقیقه دیگر با سرعت، دقت و ظاهر بهتر برمی‌گردیم.' }}
        </p>

        <div class="rx-maintain-actions">
            <a
                href="{{ $maintenance['telegram'] ?? 'https://t.me/RendixDMServices' }}"
                target="_blank"
                rel="noopener noreferrer"
                class="rx-maintain-btn rx-maintain-btn-primary"
            >
                <i class="fa-brands fa-telegram"></i>
                <span>اطلاع لحظه‌ای در تلگرام</span>
            </a>

            <button type="button" class="rx-maintain-btn rx-maintain-btn-ghost" onclick="location.reload()">
                <i class="fa-solid fa-rotate"></i>
                <span>بررسی دوباره</span>
            </button>
        </div>

        <div class="rx-maintain-footer">
            Rendix Digital Marketing — ما به‌زودی برمی‌گردیم.
        </div>
    </main>
</body>
</html>