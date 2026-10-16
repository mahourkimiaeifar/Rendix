<?php
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $phone = '';
    public string $message = '';
    public bool $submitted = false;

    public function submitForm()
    {
        $this->validate([
            'name' => 'required|min:3',
            'phone' => 'required|min:10',
            'message' => 'required|min:5',
        ]);
        $this->submitted = true;
    }

    public function rendering($view)
    {
        $view->layoutData([
            'title' => 'تماس با رندیکس | مشاوره تخصصی دیجیتال مارکتینگ',
            'metaDescription' => 'دریافت مشاوره رایگان دیجیتال مارکتینگ، سئو و طراحی وبسایت در نجف آباد. همین حالا با متخصصین رندیکس تماس بگیرید.',
            'metaKeywords' => 'دیجیتال مارکتینگ, مشاوره دیجیتال مارکتینگ, سفارش طراحی وبسایت, تماس با رندیکس نجف آباد'
        ]);
    }
}; ?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="gsap-reveal text-center max-w-2xl mx-auto mb-12">
        <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-cyan-500/10 border border-cyan-500/30 text-cyan-600 dark:text-cyan-300 mb-4 inline-block">
            مشاوره رایگان دیجیتال مارکتینگ
        </span>
        <h1 class="text-3xl sm:text-5xl font-black mb-4">فروش خود را با <span class="text-cyan-500">دیجیتال مارکتینگ</span> منفجر کنید</h1>
        <p class="text-slate-600 dark:text-slate-300 text-base sm:text-lg">
            فرم زیر را پر کنید تا استراتژیست‌های دیجیتال مارکتینگ رندیکس در نجف آباد در کمترین زمان با شما تماس بگیرند.
        </p>
    </div>

    <div class="gsap-reveal p-6 sm:p-10 rounded-3xl bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-white/10 backdrop-blur-xl shadow-2xl">
        @if($submitted)
            <div class="p-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-center font-bold">
                🎉 درخواست مشاوره دیجیتال مارکتینگ شما ثبت شد! به‌زودی با شما تماس می‌گیریم.
            </div>
        @else
            <form wire:submit="submitForm" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold mb-2">نام و نام خانوادگی</label>
                        <input type="text" wire:model="name" class="w-full px-4 py-3.5 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-300 dark:border-white/10 focus:outline-none focus:border-cyan-500 transition" placeholder="مثلاً محمد امینی">
                        @error('name') <span class="text-rose-500 text-xs mt-1 block">لطفاً نام خود را کامل وارد کنید</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2">شماره موبایل</label>
                        <input type="tel" wire:model="phone" class="w-full px-4 py-3.5 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-300 dark:border-white/10 focus:outline-none focus:border-cyan-500 transition" placeholder="09130000000">
                        @error('phone') <span class="text-rose-500 text-xs mt-1 block">شماره تماس معتبر وارد کنید</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">هدف شما از دیجیتال مارکتینگ یا طراحی سایت چیست؟</label>
                    <textarea wire:model="message" rows="4" class="w-full px-4 py-3.5 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-300 dark:border-white/10 focus:outline-none focus:border-cyan-500 transition" placeholder="توضیح مختصر درباره کسب‌وکار شما در نجف آباد یا سایر شهرها..."></textarea>
                    @error('message') <span class="text-rose-500 text-xs mt-1 block">لطفاً توضیحات کوتاهی بنویسید</span> @enderror
                </div>

                <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold shadow-lg shadow-cyan-500/25 hover:scale-[1.01] transition">
                    ثبت درخواست مشاوره رایگان 🚀
                </button>
            </form>
        @endif
    </div>
</div>