<div>

    {{-- ═══════════ هیرو ═══════════ --}}
    <section class="pt-36 pb-24 px-5 md:px-10">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-12 gap-14 items-start">

            <div class="lg:col-span-7 space-y-8">
                <div class="badge fade-up">
                    <span class="badge-dot"></span>
                    داستان رندیکس
                </div>

                <h1 class="text-[40px] md:text-6xl lg:text-7xl font-black leading-[1.15] fade-up delay-1">
                    رندیکس یه پروژه نیست،
                    <br>
                    <span class="gradient-text">یه عشقه.</span>
                </h1>

                <p class="text-lg md:text-xl text-[color:var(--rx-fg-muted)] leading-10 max-w-3xl fade-up delay-2">
                    خیلی‌ها برند می‌سازن تا پول دربیارن. ما رندیکس رو ساختیم چون به یه چیز ایمان داریم:
                    کسب‌وکارهای کوچیک و جدی، لایق بهترین‌ها هستن — نه فقط اونایی که بودجه کلان دارن.
                </p>

                <div class="flex flex-wrap gap-4 fade-up delay-3">
                    <a href="/contact" wire:navigate class="btn btn-primary">
                        بیا صحبت کنیم
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <a href="#story" class="btn btn-ghost">
                        داستان ما
                        <i class="fa-solid fa-arrow-down text-xs"></i>
                    </a>
                </div>
            </div>

            <x-site.testimonials :limit="4" title="اعتماد مشتریان رندیکس" />

            <div class="lg:col-span-5 fade-up delay-3">
                <div class="glass p-7 space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="icon-tile">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <div class="text-xs text-[color:var(--rx-fg-soft)]">ریشه ما</div>
                            <div class="font-black">نجف‌آباد، اصفهان</div>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-white/5">
                        <div class="flex justify-between text-sm">
                            <span class="text-[color:var(--rx-fg-muted)]">تمرکز اولیه</span>
                            <span class="font-bold">نجف‌آباد و اصفهان</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[color:var(--rx-fg-muted)]">پوشش خدمات</span>
                            <span class="font-bold">سراسر ایران</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[color:var(--rx-fg-muted)]">رویکرد</span>
                            <span class="font-bold">کیفیت از روز اول</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[color:var(--rx-fg-muted)]">هدف</span>
                            <span class="font-bold">رشد پایدار مشتری</span>
                        </div>
                    </div>

                    <p class="text-xs text-[color:var(--rx-fg-soft)] leading-6 pt-4 border-t border-white/5">
                        ما به شهرمون افتخار می‌کنیم، ولی برندمون رو در یک نقطه جغرافیایی حبس نمی‌کنیم. رندیکس برای
                        کسب‌وکارهایی ساخته شده که می‌خوان جدی رشد کنن.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="divider"></div>


    {{-- ═══════════ داستان ═══════════ --}}
    <section id="story" class="px-5 md:px-10 py-16 scroll-mt-24">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-12 gap-14 items-start">

            <div class="lg:col-span-7 space-y-7">
                <p class="section-label rv">چطور شروع شد؟</p>

                <h2 class="text-3xl md:text-5xl font-black leading-tight rv">
                    از یه ناراحتی ساده،
                    <br>
                    یه برند جدی ساخته شد.
                </h2>

                <div class="space-y-6 text-[color:var(--rx-fg-muted)] leading-9 rv">
                    <p>
                        ما بارها دیدیم کسب‌وکارهای خوبی که واقعاً زحمت می‌کشن، در فضای دیجیتال دیده نمی‌شن. نه چون
                        محصولشون بده، بلکه چون هیچ‌کس براشون سیستم رشد نساخته.
                    </p>
                    <p>
                        رندیکس از همینجا متولد شد: از اینکه خواستیم ثابت کنیم یه تیم جوان و باورمند می‌تونه خروجی‌ای
                        بسازه که در سطح برندهای بزرگ قابل دفاع باشه.
                    </p>
                    <p>
                        ما نمی‌خواستیم اول بی‌کیفیت شروع کنیم و بعداً بهتر بشیم. از همان روز اول تصمیم گرفتیم فقط چیزی
                        بسازیم که بهش افتخار کنیم.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-5 rv">
                <div class="glass p-8 space-y-6">
                    <i class="fa-solid fa-quote-right text-4xl text-[color:var(--rx-accent-text)] opacity-30"></i>

                    <p class="text-lg leading-9 text-[color:var(--rx-fg)]">
                        «ما دنبال پروژه نیستیم؛ دنبال داستانیم. داستان رشد کسی که به ما اعتماد می‌کنه.»
                    </p>

                    <div class="pt-6 border-t border-white/5 space-y-4">
                        <div class="flex items-center gap-3 text-sm text-[color:var(--rx-fg-muted)]">
                            <i class="fa-solid fa-heart text-[color:var(--rx-accent-3)]"></i>
                            <span>رندیکس با عشق ساخته شده.</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-[color:var(--rx-fg-muted)]">
                            <i class="fa-solid fa-seedling text-[color:var(--rx-accent-text)]"></i>
                            <span>برای رشد پایدار، نه هیجان لحظه‌ای.</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-[color:var(--rx-fg-muted)]">
                            <i class="fa-solid fa-shield-halved text-[color:var(--rx-accent-2)]"></i>
                            <span>با مسئولیت، نه فقط سفارش.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="divider"></div>


    {{-- ═══════════ باورها ═══════════ --}}
    <section class="px-5 md:px-10 py-16">
        <div class="max-w-7xl mx-auto">
            <div class="mb-14 space-y-4">
                <p class="section-label rv">باورهای ما</p>
                <h2 class="text-3xl md:text-5xl font-black rv">
                    چیزایی که
                    <span class="gradient-text">هرگز</span>
                    ازشون نمی‌گذریم
                </h2>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                @php
                    $beliefs = [
                        [
                            'icon' => 'fa-gem',
                            'title' => 'کیفیت لوکس نیست، ضروریه',
                            'desc' => 'ما باور داریم کسب‌وکارهای جدی لایق بهترین تجربه دیجیتال هستن، حتی اگه بودجه‌شون محدود باشه.',
                        ],
                        [
                            'icon' => 'fa-eye',
                            'title' => 'شفافیت، پایه اعتمادیه',
                            'desc' => 'هیچ چیز رو پنهان نمی‌کنیم. هزینه، زمان، مراحل و نتیجه باید برای مشتری روشن باشه.',
                        ],
                        [
                            'icon' => 'fa-bullseye',
                            'title' => 'هدف مهمه، نه فقط ظاهر',
                            'desc' => 'سایت قشنگ بدون هدف تجاری، فقط یه نقاشی گرونه. ما برای نتیجه می‌سازیم.',
                        ],
                        [
                            'icon' => 'fa-users',
                            'title' => 'مشتری هم‌تیمی ماست',
                            'desc' => 'ما بالا سر مشتری نمی‌ایستیم؛ کنارش می‌شینیم. بهترین نتیجه از همکاری واقعی میاد.',
                        ],
                    ];
                @endphp

                @foreach($beliefs as $belief)
                    <div class="glass glass-hover p-7 space-y-4 rv">
                        <div class="icon-tile">
                            <i class="fa-solid {{ $belief['icon'] }}"></i>
                        </div>
                        <h3 class="text-xl font-black">{{ $belief['title'] }}</h3>
                        <p class="text-sm text-[color:var(--rx-fg-muted)] leading-8">{{ $belief['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="divider"></div>


    {{-- ═══════════ چطور کار می‌کنیم ═══════════ --}}
    <section class="px-5 md:px-10 py-16">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-12 gap-14">

            <div class="lg:col-span-4">
                <div class="lg:sticky lg:top-28 space-y-5">
                    <p class="section-label rv">روش کار</p>
                    <h2 class="text-3xl md:text-5xl font-black leading-tight rv">
                        ما چطور
                        <span class="gradient-text">تصمیم</span>
                        می‌گیریم؟
                    </h2>
                    <p class="text-[color:var(--rx-fg-muted)] leading-8 rv">
                        هر تصمیمی که در پروژه می‌گیریم، باید جواب این سوال رو بده: «این کار به رشد کسب‌وکار مشتری کمک
                        می‌کنه؟»
                    </p>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-8">
                <div class="rv border-b border-white/10 pb-8">
                    <div class="flex items-start gap-5">
                        <span class="text-4xl font-black gradient-text opacity-40">۰۱</span>
                        <div>
                            <h3 class="text-xl font-black mb-2">اول گوش می‌دیم</h3>
                            <p class="text-[color:var(--rx-fg-muted)] leading-8">
                                قبل از پیشنهاد دادن، می‌فهمیم مشتری واقعاً چه دردی داره. خیلی‌ها مشکل رو اشتباه تعریف
                                می‌کنن؛ ما کمک می‌کنیم درست پیداش کنیم.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rv border-b border-white/10 pb-8">
                    <div class="flex items-start gap-5">
                        <span class="text-4xl font-black gradient-text opacity-40">۰۲</span>
                        <div>
                            <h3 class="text-xl font-black mb-2">بعد نقشه می‌کشیم</h3>
                            <p class="text-[color:var(--rx-fg-muted)] leading-8">
                                بدون برنامه وارد اجرا نمی‌شیم. مرحله‌ها، اولویت‌ها، زمان‌بندی و معیار موفقیت مشخص می‌شه.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rv border-b border-white/10 pb-8">
                    <div class="flex items-start gap-5">
                        <span class="text-4xl font-black gradient-text opacity-40">۰۳</span>
                        <div>
                            <h3 class="text-xl font-black mb-2">سپس با دقت می‌سازیم</h3>
                            <p class="text-[color:var(--rx-fg-muted)] leading-8">
                                طراحی، توسعه، سئو و محتوا باید مثل دندانه‌های چرخ‌دنده کار کنن. ما المان‌ها رو جدا
                                نمی‌بینیم؛ سیستم رو می‌سازیم.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rv">
                    <div class="flex items-start gap-5">
                        <span class="text-4xl font-black gradient-text opacity-40">۰۴</span>
                        <div>
                            <h3 class="text-xl font-black mb-2">و در آخر همراهی می‌کنیم</h3>
                            <p class="text-[color:var(--rx-fg-muted)] leading-8">
                                تحویل پروژه پایان کار نیست. ما می‌مونیم تا ببینیم سیستم واقعاً کار می‌کنه، کاربر راحت‌ه
                                و فروش بهتر شده.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="divider"></div>


    {{-- ═══════════ توانمندی‌ها ═══════════ --}}
    <section class="px-5 md:px-10 py-16">
        <div class="max-w-7xl mx-auto">
            <div class="mb-14 space-y-4">
                <p class="section-label rv">جعبه‌ابزار ما</p>
                <h2 class="text-3xl md:text-5xl font-black rv">با چی می‌سازیم؟</h2>
                <p class="text-[color:var(--rx-fg-muted)] max-w-2xl leading-8 rv">
                    ما فقط حرف نمی‌زنیم؛ با تکنولوژی‌های به‌روز و استاندارد کار می‌کنیم تا خروجی سریع، امن و مقیاس‌پذیر
                    باشه.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-5">
                @php
                    $caps = [
                        [
                            'icon' => 'fa-code',
                            'title' => 'مهندسی',
                            'desc' => 'زیرساخت تمیز، سریع و قابل توسعه.',
                            'items' => ['Laravel', 'Livewire', 'React', 'Three.js', 'Tailwind', 'Vite'],
                        ],
                        [
                            'icon' => 'fa-palette',
                            'title' => 'طراحی',
                            'desc' => 'تجربه‌ای که چشم خسته نکنه و کاربر گیج نشه.',
                            'items' => ['UI/UX', 'Motion Design', 'Branding', 'Responsive', 'Accessibility'],
                        ],
                        [
                            'icon' => 'fa-chart-line',
                            'title' => 'رشد',
                            'desc' => 'هر چیزی که باید دیده بشه، اعتماد بسازه و بفروشه.',
                            'items' => ['SEO', 'Content Strategy', 'Ads', 'Analytics', 'CRO'],
                        ],
                    ];
                @endphp

                @foreach($caps as $cap)
                    <div class="glass glass-hover p-7 space-y-5 rv">
                        <div class="icon-tile">
                            <i class="fa-solid {{ $cap['icon'] }}"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black mb-2">{{ $cap['title'] }}</h3>
                            <p class="text-sm text-[color:var(--rx-fg-muted)] leading-7">{{ $cap['desc'] }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2 pt-2">
                            @foreach($cap['items'] as $item)
                                <span
                                    class="text-xs text-[color:var(--rx-fg-muted)] bg-white/5 border border-white/10 rounded-full px-3 py-1.5">
                                    {{ $item }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="divider"></div>


    {{-- ═══════════ قول رندیکس ═══════════ --}}
    <section class="px-5 md:px-10 py-16">
        <div class="max-w-5xl mx-auto">
            <div class="glass p-8 md:p-12 space-y-8 rv">
                <div class="space-y-4 text-center">
                    <p class="section-label justify-center">تعهد ما</p>
                    <h2 class="text-3xl md:text-5xl font-black">
                        قول
                        <span class="gradient-text">رندیکس</span>
                    </h2>
                    <p class="text-[color:var(--rx-fg-muted)] max-w-2xl mx-auto leading-8">
                        ما قول‌های توخالی نمی‌دیم. این‌ها چیزاییه که روی اون‌ها کار می‌کنیم و انتظار داریم مشتری هم با
                        ما همراه باشه.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-5">
                    @php
                        $promises = [
                            'صادق بودن در پیشنهادها',
                            'شفاف بودن در هزینه و زمان',
                            'ساختن تجربه‌ای که کاربر راحت باشه',
                            'بهینه‌سازی برای سرعت و سئو',
                            'پشتیبانی واقعی بعد از تحویل',
                            'همراهی تا رسیدن به نتیجه',
                        ];
                    @endphp

                    @foreach($promises as $promise)
                        <div class="flex items-start gap-3 p-4 rounded-2xl bg-white/5 border border-white/5">
                            <i class="fa-solid fa-circle-check text-[color:var(--rx-accent-text)] mt-0.5"></i>
                            <span class="text-sm text-[color:var(--rx-fg-muted)] leading-7">{{ $promise }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <div class="divider"></div>


    {{-- ═══════════ مسیر زمانی ═══════════ --}}
    <section class="px-5 md:px-10 py-16">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-14 space-y-4">
                <p class="section-label justify-center rv">مسیر ما</p>
                <h2 class="text-3xl md:text-5xl font-black rv">
                    از اینجا شروع کردیم،
                    <br>
                    اینجا متوقف نمی‌شیم.
                </h2>
            </div>

            <div class="space-y-8">
                <div class="grid grid-cols-12 gap-6 rv">
                    <div class="col-span-12 md:col-span-2">
                        <div class="text-3xl font-black gradient-text">۱۴۰۳</div>
                    </div>
                    <div class="col-span-12 md:col-span-10 space-y-2">
                        <h3 class="text-xl font-black">تولد رندیکس</h3>
                        <p class="text-[color:var(--rx-fg-muted)] leading-8">
                            با یک باور ساده شروع کردیم: کسب‌وکارهای جدی لایق بهترین‌ها هستن. از همان اول خواستیم کیفیت
                            را جدی بگیریم، نه اینکه بعداً جبران کنیم.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6 rv">
                    <div class="col-span-12 md:col-span-2">
                        <div class="text-3xl font-black gradient-text">۱۴۰۴</div>
                    </div>
                    <div class="col-span-12 md:col-span-10 space-y-2">
                        <h3 class="text-xl font-black">رشد و شکل‌گیری هویت</h3>
                        <p class="text-[color:var(--rx-fg-muted)] leading-8">
                            رندیکس فقط یه نام نشد؛ یه سبک کاری شد. طراحی، توسعه، سئو و دیجیتال مارکتینگ را به‌صورت یک
                            سیستم رشد کنار هم قرار دادیم.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6 rv">
                    <div class="col-span-12 md:col-span-2">
                        <div class="text-3xl font-black gradient-text">آینده</div>
                    </div>
                    <div class="col-span-12 md:col-span-10 space-y-2">
                        <h3 class="text-xl font-black">افق‌های بزرگ‌تر</h3>
                        <p class="text-[color:var(--rx-fg-muted)] leading-8">
                            می‌خوایم رندیکس به یکی از معتبرترین استودیوهای دیجیتال ایران تبدیل بشه؛ جایی که کیفیت، صداقت
                            و نتیجه حرف اول را می‌زنه.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="divider"></div>


    {{-- ═══════════ برای چه کسانی ساخته شدیم؟ ═══════════ --}}
    <section class="px-5 md:px-10 py-16">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-12 gap-14 items-center">

            <div class="lg:col-span-5 space-y-6">
                <p class="section-label rv">مخاطب ما</p>
                <h2 class="text-3xl md:text-5xl font-black leading-tight rv">
                    رندیکس برای کسیه که
                    <span class="gradient-text">جدیه</span>.
                </h2>
                <p class="text-[color:var(--rx-fg-muted)] leading-9 rv">
                    ما برای کسب‌وکارهایی کار می‌کنیم که می‌خوان رشد کنن، نه فقط حاضر باشن. برای برندهایی که به مشتری
                    احترام می‌ذارن و می‌خوان تجربه‌ای بسازن که موندگار باشه.
                </p>
            </div>

            <div class="lg:col-span-7 grid sm:grid-cols-2 gap-5">
                @php
                    $audience = [
                        ['icon' => 'fa-store', 'title' => 'فروشگاه‌های اینترنتی', 'desc' => 'که می‌خوان فروش آنلاینشون را سیستماتیک बढ़ان بدن.'],
                        ['icon' => 'fa-briefcase', 'title' => 'کسب‌وکارهای خدماتی', 'desc' => 'که نیاز به اعتمادسازی دیجیتال دارن.'],
                        ['icon' => 'fa-mountain-sun', 'title' => 'برندهای محلی', 'desc' => 'که می‌خوان فراتر از شهر خودشون دیده بشن.'],
                        ['icon' => 'fa-lightbulb', 'title' => 'استارتاپ‌ها', 'desc' => 'که برای شروع قدرتمند به هویت و محصول دیجیتال نیاز دارن.'],
                    ];
                @endphp

                @foreach($audience as $item)
                    <div class="glass glass-hover p-6 space-y-3 rv">
                        <div class="icon-tile !w-12 !h-12 !text-lg">
                            <i class="fa-solid {{ $item['icon'] }}"></i>
                        </div>
                        <h3 class="font-black">{{ $item['title'] }}</h3>
                        <p class="text-sm text-[color:var(--rx-fg-muted)] leading-7">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="divider"></div>


    {{-- ═══════════ CTA نهایی ═══════════ --}}
    <section class="px-5 md:px-10 pb-16">
        <div class="max-w-5xl mx-auto">
            <div class="glass p-10 md:p-16 text-center space-y-8 relative overflow-hidden rv">
                <div class="absolute inset-0 pointer-events-none"
                    style="background: radial-gradient(520px circle at 50% 0%, rgba(118,83,246,0.14), transparent 62%)">
                </div>

                <h2 class="text-3xl md:text-6xl font-black leading-tight relative">
                    دوست داری داستان بعدی،
                    <span class="gradient-text">داستان تو</span>
                    باشه؟
                </h2>

                <p class="text-lg text-[color:var(--rx-fg-muted)] max-w-2xl mx-auto leading-9 relative">
                    ما آماده‌ایم گوش بدیم. اگه کسب‌وکارت برات مهمه، بیا با هم یه مسیر رشد براش طراحی کنیم.
                </p>

                <div class="flex flex-wrap justify-center gap-4 relative">
                    <a href="/contact" wire:navigate class="btn btn-primary !px-10 !py-4 !text-base">
                        شروع گفتگو
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <a href="/" wire:navigate class="btn btn-ghost !px-10 !py-4 !text-base">
                        بازگشت به صفحه اصلی
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>