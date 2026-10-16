<?php
use Livewire\Volt\Component;

new class extends Component {
    public bool $mobileMenuOpen = false;
}; ?>

<header class="fixed top-4 inset-x-0 z-50 px-4 sm:px-6 lg:px-8">
    <nav class="max-w-6xl mx-auto h-16 px-6 rounded-2xl backdrop-blur-2xl bg-white/85 dark:bg-slate-900/80 border border-slate-200/90 dark:border-slate-800/80 shadow-xl shadow-slate-900/5 dark:shadow-black/40 flex items-center justify-between transition-all duration-300">
        
        <!-- برند و لوگو -->
        <a href="/" wire:navigate class="flex items-center gap-2.5 group">
            <span class="w-3 h-3 rounded-full bg-cyan-500 group-hover:scale-125 transition shadow-[0_0_12px_#06b6d4]"></span>
            <span class="text-xl font-black tracking-wider text-slate-900 dark:text-white">
                RENDIX<span class="text-cyan-500">.IR</span>
            </span>
        </a>

        <!-- لینک‌های ناوبری با کنتراست بالا -->
        <div class="hidden md:flex items-center gap-1 bg-slate-100/80 dark:bg-slate-950/60 p-1.5 rounded-xl border border-slate-200/60 dark:border-slate-800/60">
            <a href="/" wire:navigate class="px-4 py-1.5 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-white dark:hover:bg-slate-800 hover:text-cyan-600 dark:hover:text-cyan-400 transition">
                صفحه اصلی
            </a>
            <a href="/about" wire:navigate class="px-4 py-1.5 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-white dark:hover:bg-slate-800 hover:text-cyan-600 dark:hover:text-cyan-400 transition">
                درباره رندیکس (نجف آباد)
            </a>
            <a href="/contact" wire:navigate class="px-4 py-1.5 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-white dark:hover:bg-slate-800 hover:text-cyan-600 dark:hover:text-cyan-400 transition">
                مشاوره دیجیتال مارکتینگ
            </a>
        </div>

        <!-- ابزارها -->
        <div class="flex items-center gap-2.5">
            <button onclick="window.toggleRendixTheme()" type="button" aria-label="تغییر تم" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700 text-slate-700 dark:text-amber-400 hover:border-cyan-500/50 transition">
                <svg class="w-4 h-4 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <svg class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m3.343-5.657l-.707-.707m12.728 12.728l-.707-.707M6.343 17.657l-.707.707m12.728-12.728l-.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
            </button>

            <a href="/contact" wire:navigate class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 dark:bg-cyan-500 text-white dark:text-slate-950 font-black text-xs sm:text-sm hover:opacity-90 transition shadow-md">
                <span>شروع پروژه</span>
                <span>←</span>
            </a>

            <button wire:click="$toggle('mobileMenuOpen')" class="md:hidden p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
            </button>
        </div>
    </nav>

    <!-- منوی موبایل -->
    @if($mobileMenuOpen)
        <div class="md:hidden max-w-6xl mx-auto mt-2 p-4 rounded-2xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-2xl border border-slate-200 dark:border-slate-800 shadow-2xl flex flex-col gap-2 font-bold text-sm">
            <a href="/" wire:navigate class="p-3 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">صفحه اصلی</a>
            <a href="/about" wire:navigate class="p-3 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">درباره ما (نجف آباد)</a>
            <a href="/contact" wire:navigate class="p-3 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">تماس و مشاوره دیجیتال مارکتینگ</a>
        </div>
    @endif
</header>