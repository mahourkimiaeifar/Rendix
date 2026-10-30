@props([
    'location' => null,
    'title' => 'سوالات متداول',
    'subtitle' => 'هرچه لازم دانی بدانی، اینجاست؛ و اگر نبود، یک پیام بده.',
    'limit' => 6,
])

@php
    $items = collect();

    if (class_exists(\App\Models\Faq::class)) {
        $fetch = function (callable $mutate) {
            try {
                $q = \App\Models\Faq::query();
                $mutate($q);
                return $q->get();
            } catch (\Throwable $e) {
                return collect();
            }
        };

        $attempts = [];

        if ($location) {
            $attempts[] = fn ($q) => $q->where('is_active', true)->where('location', $location)->orderBy('sort_order')->orderByDesc('id');
            $attempts[] = fn ($q) => $q->where('location', $location)->orderByDesc('id');
        }

        $attempts[] = fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->orderByDesc('id');
        $attempts[] = fn ($q) => $q->orderBy('sort_order')->orderByDesc('id');
        $attempts[] = fn ($q) => $q->orderByDesc('id');

        foreach ($attempts as $attempt) {
            $result = $fetch($attempt);

            if ($result->isNotEmpty()) {
                $items = $result;
                break;
            }
        }

        if ($items->count() > $limit) {
            $items = $items->take($limit);
        }
    }

    if ($items->isEmpty()) {
        $items = collect([
            (object) ['question' => 'هزینه طراحی وبسایت چقدر است؟', 'answer' => 'بستگی به دامنه پروژه دارد؛ یک وبسایت شرکتی با چند صفحه، هزینه‌ای کمتر از یک فروشگاه اینترنتی کامل دارد. بعد از بررسی نیازت، پیشنهاد شفاف با قیمت دقیق می‌فرستیم — بدون هزینه پنهان.'],
            (object) ['question' => 'تحویل پروژه چقدر طول می‌کشد؟', 'answer' => 'معمولاً بین ۲ تا ۶ هفته. وبسایت‌های شرکتی ساده سریع‌تر و فروشگاه‌ها و پروژه‌های اختصاصی کمی بیشتر. زمان‌بندی دقیق را قبل از شروع، کتبی توافق می‌کنیم.'],
            (object) ['question' => 'بعد از تحویل، پشتیبانی هم دارید؟', 'answer' => 'بله. همه پروژه‌ها با دوره پشتیبانی رایگان تحویل می‌شوند و بعد از آن هم می‌توانیم برنامه پشتیبانی ماهانه (فنی، امنیتی و محتوایی) داشته باشیم.'],
            (object) ['question' => 'سئو هم روی سایت انجام می‌شود؟', 'answer' => 'سئوی تکنیکال (سرعت، ساختار، اسکیما، sitemap) روی همه پروژه‌ها به‌صورت پایه انجام می‌شود. سئوی محتوایی و محلی هم به‌صورت بسته جدا قابل اضافه کردن است.'],
            (object) ['question' => 'مالکیت دامنه، هاست و کدها با کیست؟', 'answer' => 'کاملاً با تو. همه چیز به نام خودت ثبت می‌شود و در پایان، دسترسی کامل و سورس پروژه تحویل داده می‌شود.'],
            (object) ['question' => 'چگونه شروع کنیم؟', 'answer' => 'فرم «درخواست پروژه» را پر کن یا در تلگرام پیام بده. یک گفتگوی کوتاه ۱۵ دقیقه‌ای داریم، بعد پیشنهاد شفاف و زمان‌بندی را دریافت می‌کنی.'],
        ]);
    }
@endphp

@once
<style>
.rx-faq3{padding:56px 0}
.rx-faq3-head{text-align:center;margin-bottom:26px}
.rx-faq3-head h2{margin:0;font-size:clamp(24px,3.4vw,38px);font-weight:950;color:var(--rx-fg,#F7F9FF)}
.rx-faq3-head p{margin:10px auto 0;max-width:560px;color:var(--rx-fg-muted,rgba(247,249,255,.62));line-height:2;font-size:14.5px}
.rx-faq3-list{max-width:820px;margin-inline:auto;display:grid;gap:12px}
.rx-faq3-item{border-radius:18px;border:1px solid var(--rx-border,rgba(255,255,255,.08));background:var(--rx-panel-2,rgba(255,255,255,.025));overflow:hidden;transition:border-color .25s ease,background .25s ease}
.rx-faq3-item[open]{border-color:rgba(69,200,255,.28);background:rgba(69,200,255,.05)}
.rx-faq3-q{list-style:none;cursor:pointer;padding:16px 18px;display:flex;align-items:center;gap:14px;justify-content:space-between;color:var(--rx-fg,#F7F9FF);font-weight:900;font-size:14.5px;user-select:none;-webkit-tap-highlight-color:transparent;pointer-events:auto!important}
.rx-faq3-q::-webkit-details-marker{display:none}
.rx-faq3-q::marker{content:''}
.rx-faq3-num{flex-shrink:0;width:30px;height:30px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:950;color:var(--rx-accent,#45C8FF);background:rgba(69,200,255,.10);border:1px solid rgba(69,200,255,.18);transition:all .25s ease}
.rx-faq3-item[open] .rx-faq3-num{color:#fff;background:linear-gradient(135deg,var(--rx-accent-2,#1769FF),var(--rx-accent,#45C8FF));border-color:transparent;transform:rotate(-6deg)}
.rx-faq3-q .txt{flex:1;min-width:0}
.rx-faq3-ic{color:var(--rx-fg-soft,rgba(247,249,255,.36));font-size:12px;transition:transform .25s ease,color .25s ease}
.rx-faq3-item[open] .rx-faq3-ic{transform:rotate(45deg);color:var(--rx-accent,#45C8FF)}
.rx-faq3-a{padding:0 18px 18px 62px;color:var(--rx-fg-muted,rgba(247,249,255,.62));font-size:13.5px;line-height:2.2;animation:rxfaq3o .28s ease}
@keyframes rxfaq3o{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:none}}
.rx-faq3-foot{text-align:center;margin-top:20px}
.rx-faq3-foot a{color:var(--rx-accent-text,#45C8FF);font-size:13px;font-weight:850;text-decoration:none;border-bottom:1px dashed rgba(69,200,255,.4);transition:border-color .2s ease}
.rx-faq3-foot a:hover{border-bottom-color:var(--rx-accent,#45C8FF);border-bottom-style:solid}
@media(max-width:640px){.rx-faq3-a{padding-inline-start:18px}}
@media(prefers-reduced-motion:reduce){.rx-faq3-a{animation:none!important}.rx-faq3-num,.rx-faq3-ic{transition:none!important}}
</style>
@endonce

<section class="rx-faq3">
    <div class="rx-container">
        <div class="rx-faq3-head">
            <h2>{{ $title }}</h2>
            <p>{{ $subtitle }}</p>
        </div>

        <div class="rx-faq3-list">
            @foreach ($items as $i => $faq)
                <details class="rx-faq3-item" @if ($i === 0) open @endif>
                    <summary class="rx-faq3-q">
                        <span class="rx-faq3-num">{{ \App\Support\Jalali::toPersianDigits((string) ($i + 1)) }}</span>
                        <span class="txt">{{ $faq->question }}</span>
                        <i class="fa-solid fa-plus rx-faq3-ic" aria-hidden="true"></i>
                    </summary>
                    <div class="rx-faq3-a">{{ $faq->answer }}</div>
                </details>
            @endforeach
        </div>

        @if (Route::has('contact'))
            <div class="rx-faq3-foot">
                <a href="{{ route('contact') }}" wire:navigate>سوال دیگری داری؟ همین‌جا بپرس</a>
            </div>
        @endif
    </div>
</section>