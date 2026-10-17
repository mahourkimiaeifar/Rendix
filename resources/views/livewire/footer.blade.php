<div>
    <footer class="relative mt-32 border-t border-white/5 bg-navy-2/40">
        <div class="max-w-7xl mx-auto px-5 md:px-10 py-16">

            {{-- بالا: برند + خبرنامه --}}
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8 pb-12 border-b border-white/5">
                <div class="space-y-3 max-w-md">
                    <div class="flex items-center gap-3">
                        <img src="/images/favicon.png?v=6" alt="رندیکس" class="w-11 h-11">
                        <span class="text-2xl font-black">رندیکس</span>
                    </div>
                    <p class="text-sm text-snow/50 leading-7">
                        ما فقط وبسایت نمی‌سازیم؛ مسیر رشد می‌سازیم. از نجف‌آباد، برای کسب‌وکارهایی که می‌خوان جدی دیده بشن.
                    </p>
                </div>

                <div class="w-full max-w-md">
                    <p class="text-sm font-bold mb-3 text-snow/70">عضویت در خبرنامه رشد</p>
                    <form class="flex gap-2">
                        <input type="email" placeholder="ایمیل شما"
                               class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm placeholder:text-snow/30 focus:outline-none focus:border-sky/50 transition-colors">
                        <button type="submit" class="btn btn-primary !px-5">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- لینک‌ها --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-10 py-12">
                <div class="space-y-4">
                    <p class="font-bold text-sm">رندیکس</p>
                    <ul class="space-y-3 text-sm text-snow/50">
                        <li><a href="/about" wire:navigate class="hover:text-sky transition-colors">درباره ما</a></li>
                        <li><a href="/contact" wire:navigate class="hover:text-sky transition-colors">تماس با ما</a></li>
                        <li><a href="/#faq" class="hover:text-sky transition-colors">سوالات متداول</a></li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <p class="font-bold text-sm">خدمات</p>
                    <ul class="space-y-3 text-sm text-snow/50">
                        <li><a href="/#services" class="hover:text-sky transition-colors">دیجیتال مارکتینگ</a></li>
                        <li><a href="/#services" class="hover:text-sky transition-colors">طراحی وبسایت</a></li>
                        <li><a href="/#services" class="hover:text-sky transition-colors">سئو</a></li>
                        <li><a href="/#services" class="hover:text-sky transition-colors">فروشگاه اینترنتی</a></li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <p class="font-bold text-sm">تماس</p>
                    <ul class="space-y-3 text-sm text-snow/50">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-location-dot text-sky/70"></i> نجف‌آباد، اصفهان</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-envelope text-sky/70"></i> info@rendix.ir</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-phone text-sky/70"></i> ۰۹۱۲ ۳۴۵ ۶۷۸۹</li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <p class="font-bold text-sm">همراه ما باشید</p>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-snow/60 hover:text-sky hover:border-sky/40 transition-all"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-snow/60 hover:text-sky hover:border-sky/40 transition-all"><i class="fa-brands fa-telegram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-snow/60 hover:text-sky hover:border-sky/40 transition-all"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>

            {{-- پایین --}}
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 pt-8 border-t border-white/5 text-xs text-snow/35">
                <p>© ۱۴۰۴ رندیکس — تمامی حقوق محفوظ است.</p>
                <p class="flex items-center gap-1.5">ساخته‌شده با <i class="fa-solid fa-heart text-primary"></i> در نجف‌آباد</p>
            </div>
        </div>
    </footer>
</div>