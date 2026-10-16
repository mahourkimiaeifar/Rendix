<?php
use Livewire\Volt\Component;

new class extends Component {
    public function rendering($view)
    {
        $view->layoutData([
            'title' => 'درباره رندیکس | تیم تخصصی طراحی سایت در نجف آباد',
            'metaDescription' => 'آشنایی با شرکت رندیکس در نجف آباد؛ تیمی از متخصصین برنامه‌نویسی، طراحی وبسایت سه‌بعدی و دیجیتال مارکتینگ.',
            'metaKeywords' => 'نجف آباد, شرکت طراحی سایت نجف آباد, برنامه نویسی نجف آباد, درباره رندیکس'
        ]);
    }
}; ?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="gsap-reveal text-center max-w-3xl mx-auto mb-16">
        <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-purple-500/10 border border-purple-500/30 text-purple-600 dark:text-purple-300 mb-4 inline-block">
            داستان شکل‌گیری ما در نجف آباد
        </span>
        <h1 class="text-3xl sm:text-5xl font-black mb-6 leading-tight">
            پیشگامان تحول دیجیتال در <span class="text-cyan-500">نجف آباد</span>
        </h1>
        <p class="text-slate-600 dark:text-slate-300 text-base sm:text-lg leading-relaxed">
            آژانس دیجیتال <strong class="text-slate-900 dark:text-white">رندیکس</strong> از قلب شهر <strong class="text-purple-500">نجف آباد</strong> کار خود را با یک هدف بزرگ آغاز کرد: آوردن جدیدترین تکنولوژی‌های وب جهان (از هوش مصنوعی تا گرافیک سه‌بعدی) به کسب‌وکارهای صنعتی، تجاری و خدماتی منطقه.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="gsap-reveal p-8 rounded-3xl bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-white/10 backdrop-blur-xl shadow-xl">
            <h2 class="text-2xl font-black mb-4 text-cyan-500">چرا کسب‌وکارهای نجف آباد ما را انتخاب می‌کنند؟</h2>
            <ul class="space-y-3 text-slate-600 dark:text-slate-300 text-sm sm:text-base">
                <li>✅ امکان برگزاری جلسات حضوری در نجف آباد</li>
                <li>✅ شناخت دقیق بازار و رقبا در نجف آباد و استان اصفهان</li>
                <li>✅ طراحی بدون رفرش (SPA) با بالاترین نمره سئو گوگل</li>
                <li>✅ پشتیبانی فنی ۲۴ ساعته و تضمین کیفیت</li>
            </ul>
        </div>

        <div class="gsap-reveal p-8 rounded-3xl bg-gradient-to-br from-cyan-500/10 to-purple-500/10 border border-cyan-500/30 backdrop-blur-xl flex flex-col justify-center">
            <h2 class="text-2xl font-black mb-4">ماموریت ما</h2>
            <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                ما معتقدیم یک وبسایت نباید فقط یک کاتالوگ آنلاین باشد؛ بلکه باید قوی‌ترین فروشنده ۲۴ ساعته شما در نجف آباد و سراسر ایران باشد.
            </p>
        </div>
    </div>
</div>