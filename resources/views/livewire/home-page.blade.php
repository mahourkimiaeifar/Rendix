<?php
use Livewire\Volt\Component;

new class extends Component {
    public function rendering($view)
    {
        $view->layoutData([
            'title' => 'رندیکس | طراحی وبسایت و دیجیتال مارکتینگ در نجف آباد',
            'metaDescription' => 'آژانس مهندسی رندیکس؛ پیشرو در طراحی وبسایت اختصاصی، سئو تضمینی و دیجیتال مارکتینگ در نجف آباد. افزایش فروش کسب‌وکار شما با تکنولوژی روز.',
            'metaKeywords' => 'طراحی وبسایت, دیجیتال مارکتینگ, نجف آباد, طراحی سایت نجف آباد, سئو سایت در نجف آباد, رندیکس'
        ]);
    }
}; ?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-20">
    
    <!-- ۱. بخش Hero با تمرکز، کنتراست بالا و سلسله‌مراتب بصری دقیق -->
    <section class="min-h-[75vh] flex flex-col items-center justify-center text-center">
        
        <div class="gsap-reveal inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-900/5 dark:bg-white/5 border border-slate-300/80 dark:border-slate-800 backdrop-blur-md mb-8">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
            <span class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-300">
                مهندسی وب و رشد کسب‌وکار در <strong class="text-cyan-600 dark:text-cyan-400">نجف آباد</strong> و سراسر ایران
            </span>
        </div>

        <h1 class="gsap-reveal text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight text-slate-900 dark:text-white leading-[1.2] max-w-4xl mb-6">
            معماری نسل جدید <br />
            <span class="bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-500 bg-clip-text text-transparent">
                طراحی وبسایت و دیجیتال مارکتینگ
            </span>
        </h1>

        <p class="gsap-reveal text-slate-600 dark:text-slate-300 max-w-2xl text-base sm:text-lg lg:text-xl leading-relaxed font-medium mb-10">
            ما در <strong class="text-slate-900 dark:text-white font-black">رندیکس</strong> فقط کد نمی‌زنیم؛ ما با ترکیب سرعت خیره‌کننده، روانشناسی فروش و سئوی محلی قدرتمند در <strong class="text-slate-900 dark:text-white font-black">نجف آباد</strong>، بازدیدکنندگان شما را به مشتریان وفادار تبدیل می‌کنیم.
        </p>

        <div class="gsap-reveal flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto">
            <a href="/contact" wire:navigate class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-slate-900 dark:bg-cyan-500 text-white dark:text-slate-950 font-black text-base shadow-xl shadow-cyan-500/15 hover:-translate-y-0.5 transition">
                دریافت استراتژی رایگان دیجیتال مارکتینگ ←
            </a>
            <a href="/about" wire:navigate class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-white/90 dark:bg-slate-900/90 border border-slate-300 dark:border-slate-800 text-slate-800 dark:text-slate-200 font-bold text-base hover:border-cyan-500/50 transition backdrop-blur-md">
                آشنایی با تیم نجف آباد
            </a>
        </div>

        <!-- نوار آمار اعتمادساز -->
        <div class="gsap-reveal grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-8 w-full max-w-4xl mt-16 p-6 rounded-3xl bg-white/85 dark:bg-slate-900/80 border border-slate-200/90 dark:border-slate-800/90 backdrop-blur-xl shadow-lg">
            <div>
                <div class="text-2xl sm:text-3xl font-black text-cyan-600 dark:text-cyan-400">+۹۹٪</div>
                <div class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400 mt-1">پرفورمنس و سرعت</div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">رتبه ۱</div>
                <div class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400 mt-1">سئو محلی نجف آباد</div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">۳ برابر</div>
                <div class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400 mt-1">رشد نرخ تبدیل (CRO)</div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black text-emerald-500">۲۴/۷</div>
                <div class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400 mt-1">پشتیبانی اختصاصی</div>
            </div>
        </div>
    </section>

    <!-- ۲. بخش Bento Grid خدمات تخصصی (کنتراست و خوانایی فوق‌العاده) -->
    <section class="py-16">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white mb-4">
                اکوسیستم خدمات تخصصی رندیکس
            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base">
                همه‌چیز برای صدرنشینی برند شما در نتایج گوگل و بازار نجف آباد، در یک پکیج مهندسی‌شده.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- کارت بزرگ ۱: طراحی وبسایت -->
            <article class="md:col-span-2 p-8 rounded-3xl bg-white/90 dark:bg-slate-900/85 border border-slate-200/90 dark:border-slate-800 backdrop-blur-xl shadow-xl hover:border-cyan-500/50 transition flex flex-col justify-between">
                <div>
                    <span class="inline-block px-3 py-1 rounded-lg bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 text-xs font-black mb-4">
                        WEB ENGINEERING
                    </span>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-3">
                        طراحی وبسایت اختصاصی و فوق‌سریع
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base leading-relaxed">
                        بدون قالب‌های آماده و کند! ما وبسایت‌های شرکتی، فروشگاهی و صنعتی را با معماری مدرن Laravel و React بدون کوچک‌ترین رفرش صفحه (SPA) طراحی می‌کنیم تا کاربر حس پرواز و کنترل کامل داشته باشد.
                    </p>
                </div>
                <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap gap-2 text-xs font-bold text-slate-500 dark:text-slate-400">
                    <span class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800">رابط کاربری اختصاصی UI/UX</span>
                    <span class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800">امنیت بانکی</span>
                    <span class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800">ریسپانسیو دقیق موبایل</span>
                </div>
            </article>

            <!-- کارت ۲: دیجیتال مارکتینگ -->
            <article class="p-8 rounded-3xl bg-white/90 dark:bg-slate-900/85 border border-slate-200/90 dark:border-slate-800 backdrop-blur-xl shadow-xl hover:border-indigo-500/50 transition flex flex-col justify-between">
                <div>
                    <span class="inline-block px-3 py-1 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-xs font-black mb-4">
                        GROWTH & SEO
                    </span>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-3">
                        دیجیتال مارکتینگ داده‌محور
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                        تدوین قیف فروش، کمپین‌های تبلیغاتی هدفمند و بهینه‌سازی نرخ تبدیل برای به حداکثر رساندن بازگشت سرمایه (ROI).
                    </p>
                </div>
                <a href="/contact" wire:navigate class="mt-6 text-sm font-black text-indigo-600 dark:text-indigo-400 hover:underline">
                    شروع کمپین فروش ←
                </a>
            </article>

            <!-- کارت ۳: سئو محلی نجف آباد -->
            <article class="md:col-span-3 p-8 rounded-3xl bg-gradient-to-l from-slate-900 to-slate-800 dark:from-slate-900 dark:to-slate-950 border border-slate-800 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="max-w-2xl">
                    <span class="inline-block px-3 py-1 rounded-lg bg-cyan-500/20 text-cyan-300 text-xs font-black mb-3">
                        ویژه کسب‌وکارهای نجف آباد و اصفهان
                    </span>
                    <h3 class="text-2xl font-black mb-2">
                        سلطه کامل بر نتایج جستجوی گوگل در نجف آباد
                    </h3>
                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                        وقتی مشتریان در نجف آباد یا سراسر ایران خدمات شما را در گوگل جستجو می‌کنند، نام برند شما باید اولین انتخابی باشد که می‌بینند.
                    </p>
                </div>
                <a href="/contact" wire:navigate class="shrink-0 px-6 py-3.5 rounded-xl bg-cyan-500 text-slate-950 font-black text-sm hover:bg-cyan-400 transition">
                    رزرو جلسه حضوری یا آنلاین
                </a>
            </article>
        </div>
    </section>

    <!-- ۳. بخش سوالات متداول برای تقویت شدید سئو (SEO FAQ) -->
    <section class="py-12 max-w-4xl mx-auto">
        <h2 class="text-2xl sm:text-3xl font-black text-center text-slate-900 dark:text-white mb-8">
            سوالات متداول طراحی وبسایت و دیجیتال مارکتینگ
        </h2>
        <div class="space-y-4">
            <div class="p-6 rounded-2xl bg-white/90 dark:bg-slate-900/85 border border-slate-200/90 dark:border-slate-800 backdrop-blur-xl">
                <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white mb-2">
                    چرا طراحی وبسایت اختصاصی رندیکس از سایت‌های وردپرسی برتر است؟
                </h3>
                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                    سایت‌های اختصاصی رندیکس با هسته لاراول و ری‌اکت کدنویسی می‌شوند؛ این یعنی امنیت غیرقابل نفوذ، لود شدن زیر ۱ ثانیه، عدم وابستگی به افزونه‌های سنگین و کسب بالاترین امتیاز سئو از الگوریتم‌های گوگل.
                </p>
            </div>
            <div class="p-6 rounded-2xl bg-white/90 dark:bg-slate-900/85 border border-slate-200/90 dark:border-slate-800 backdrop-blur-xl">
                <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white mb-2">
                    آیا امکان جلسه حضوری برای طراحی سایت و دیجیتال مارکتینگ در نجف آباد وجود دارد؟
                </h3>
                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                    بله! تیم رندیکس مستقر در نجف آباد است و برای تمام کسب‌وکارهای صنعتی، فروشگاهی و خدماتی نجف آباد و استان اصفهان امکان برگزاری جلسات مشاوره حضوری، عقد قرارداد رسمی و پشتیبانی مستقیم را فراهم کرده است.
                </p>
            </div>
        </div>
    </section>

</div>