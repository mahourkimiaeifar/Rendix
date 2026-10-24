<div>
    <nav id="navbar" class="fixed top-0 inset-x-0 z-50 px-5 md:px-10">
        <div class="max-w-7xl mx-auto flex items-center justify-between h-[72px]">

            {{-- لوگو --}}
            <a href="/" wire:navigate class="flex items-center gap-3 group">
                <img src="/images/favicon.png?v=6" alt="رندیکس"
                    class="w-10 h-10 object-contain transition-transform duration-300 group-hover:scale-105">
                <span class="hidden md:inline text-lg font-black text-[var(--rx-fg)]">
                    رندیکس
                </span>
            </a>

            {{-- منوی دسکتاپ --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="/" wire:navigate
                    class="text-sm font-medium transition-colors duration-300 hover:text-[var(--rx-accent-text)] {{ request()->is('/') ? 'text-[var(--rx-accent-text)] font-bold' : 'text-[var(--rx-fg-muted)]' }}">
                    خانه
                </a>

                <a href="{{ route('services') }}" wire:navigate class="hover:text-sky transition-colors">
                    خدمات
                </a>

                <a href="/about" wire:navigate
                    class="text-sm font-medium transition-colors duration-300 hover:text-[var(--rx-accent-text)] {{ request()->is('about') ? 'text-[var(--rx-accent-text)] font-bold' : 'text-[var(--rx-fg-muted)]' }}">
                    درباره ما
                </a>

                <a href="/contact" wire:navigate
                    class="text-sm font-medium transition-colors duration-300 hover:text-[var(--rx-accent-text)] {{ request()->is('contact') ? 'text-[var(--rx-accent-text)] font-bold' : 'text-[var(--rx-fg-muted)]' }}">
                    تماس
                </a>

                <a href="{{ route('projects') }}">نمونه‌کارها</a>

                <a href="{{ route('request-project') }}" wire:navigate>درخواست پروژه</a>

                @auth
                    <a href="{{ route('dashboard') }}" wire:navigate class="hover:text-sky transition-colors">
                        داشبورد من
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="hover:text-sky transition-colors bg-transparent border-0 p-0 font-inherit text-inherit cursor-pointer">
                            خروج
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="hover:text-sky transition-colors">
                        ورود
                    </a>

                    <a href="{{ route('register') }}" wire:navigate class="btn btn-primary !py-2 !px-4 text-sm">
                        ثبت‌نام
                    </a>
                @endauth
                <x-site.admin-link />
            </div>

            {{-- اکشن‌ها --}}
            <div class="flex items-center gap-3">

                {{-- دکمه تم --}}
                <button type="button" data-theme-toggle class="theme-toggle" aria-label="تغییر تم">
                    <i class="fa-solid fa-moon"></i>
                    <i class="fa-solid fa-sun"></i>
                </button>

                {{-- CTA دسکتاپ --}}
                <a href="/contact" wire:navigate class="btn btn-primary hidden md:inline-flex !py-2.5 !px-6 !text-sm">
                    مشاوره رایگان
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </a>

                {{-- منوی موبایل --}}
                <button type="button" id="mobile-menu-toggle" class="theme-toggle md:hidden !w-11 !h-11"
                    aria-label="باز و بسته کردن منو">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>

        {{-- منوی موبایل --}}
        <div id="mobile-menu" class="hidden md:hidden glass mx-4 mb-4 p-4 space-y-1">
            <a href="/" wire:navigate
                class="block py-3 px-4 rounded-xl text-[var(--rx-fg-muted)] hover:text-[var(--rx-accent-text)] hover:bg-[var(--rx-panel)] transition-colors">
                خانه
            </a>

            <a href="{{ route('services') }}" wire:navigate class="hover:text-sky transition-colors">
                خدمات
            </a>

            <a href="/about" wire:navigate
                class="block py-3 px-4 rounded-xl text-[var(--rx-fg-muted)] hover:text-[var(--rx-accent-text)] hover:bg-[var(--rx-panel)] transition-colors">
                درباره ما
            </a>

            <a href="/contact" wire:navigate
                class="block py-3 px-4 rounded-xl text-[var(--rx-fg-muted)] hover:text-[var(--rx-accent-text)] hover:bg-[var(--rx-panel)] transition-colors">
                تماس
            </a>

            <a href="{{ route('projects') }}">نمونه‌کارها</a>

            <a href="{{ route('request-project') }}" wire:navigate>درخواست پروژه</a>

            @auth
                <a href="{{ route('dashboard') }}" wire:navigate class="hover:text-sky transition-colors">
                    داشبورد من
                </a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="hover:text-sky transition-colors bg-transparent border-0 p-0 font-inherit text-inherit cursor-pointer">
                        خروج
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" wire:navigate class="hover:text-sky transition-colors">
                    ورود
                </a>

                <a href="{{ route('register') }}" wire:navigate class="btn btn-primary !py-2 !px-4 text-sm">
                    ثبت‌نام
                </a>
            @endauth
            <x-site.admin-link />
            <a href="/contact" wire:navigate class="btn btn-primary w-full mt-3">
                مشاوره رایگان
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
        </div>
    </nav>
</div>