<!DOCTYPE html>
<html lang="fa" dir="rtl" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'رندیکس | طراحی وبسایت و دیجیتال مارکتینگ در نجف آباد' }}</title>
    <meta name="description"
        content="{{ $metaDescription ?? 'شرکت رندیکس ارائه دهنده تخصصی خدمات طراحی وبسایت، سئو و دیجیتال مارکتینگ در نجف آباد و سراسر کشور.' }}">
    <meta name="keywords"
        content="{{ $metaKeywords ?? 'طراحی وبسایت, دیجیتال مارکتینگ, نجف آباد, طراحی سایت نجف آباد, سئو, رندیکس' }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- جلوگیری از پرش رنگ در دارک/لایت مود -->
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @livewireStyles
</head>

<body
    class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white antialiased overflow-x-hidden selection:bg-cyan-500 selection:text-black transition-colors duration-500">

    <!-- ۱. صفحه لودینگ اولیه و هنگام جابجایی صفحات -->
    <div id="rendix-preloader"
        class="fixed inset-0 z-[100] bg-slate-950 flex flex-col items-center justify-center transition-opacity duration-700">
        <div class="w-16 h-16 border-4 border-cyan-500/20 border-t-cyan-400 rounded-full animate-spin mb-4"></div>
        <span
            class="text-xl font-black tracking-widest bg-gradient-to-r from-cyan-400 to-purple-500 bg-clip-text text-transparent animate-pulse">
            RENDIX.IR
        </span>
    </div>

    <!-- ۲. بوم سه بعدی ثابت در پس‌زمینه -->
    @persist('three-canvas')
    <div wire:ignore id="rendix-canvas-container" class="fixed inset-0 z-0 pointer-events-none"></div>
    @endpersist

    <!-- ۳. نوبار شیشه‌ای ریسپانسیو -->
    <livewire:navbar />

    <!-- ۴. محتوای اصلی صفحات -->
    <main class="relative z-10 pt-24 min-h-screen">
        {{ $slot }}
    </main>

    <!-- ۵. فوتر حرفه‌ای -->
    <livewire:footer />

    @livewireScripts
</body>

</html>