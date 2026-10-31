<?php

use App\Models\Template;
use App\Models\TemplateReport;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')]
    class extends Component {
    public string $slug = '';

    public string $reporter_email = '';
    public string $report_description = '';
    public bool $showReportForm = false;
    public bool $reportSent = false;

    public function mount(string $slug): void
    {
        $this->slug = trim($slug);

        $template = $this->template();

        if ($template) {
            try {
                $template->incrementViewsCount();
            } catch (\Throwable $e) {
            }
        }
    }

    public function template(): ?Template
    {
        try {
            return Template::query()
                ->with(['languages', 'files'])
                ->where('slug', $this->slug)
                ->where('is_active', true)
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function submitReport(): void
    {
        $template = $this->template();

        if (!$template) {
            return;
        }

        $this->validate([
            'reporter_email' => ['required', 'email', 'max:190'],
            'report_description' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'reporter_email.required' => 'ایمیل را وارد کن.',
            'reporter_email.email' => 'ایمیل معتبر نیست.',
            'report_description.required' => 'توضیح خرابی را بنویس.',
            'report_description.min' => 'توضیح باید حداقل ۱۰ کاراکتر باشد.',
        ]);

        TemplateReport::create([
            'template_id' => $template->id,
            'reporter_email' => strtolower(trim($this->reporter_email)),
            'description' => trim($this->report_description),
        ]);

        $this->reset(['reporter_email', 'report_description']);
        $this->showReportForm = false;
        $this->reportSent = true;

        $this->dispatch('rx-toast', ['type' => 'success', 'message' => 'گزارش خرابی ثبت شد؛ ممنون که کمک می‌کنی.']);
    }
};

?>

@php
    $template = $this->template();

    if (!$template) {
        abort(404);
    }

    $siteName = setting('site.name', 'رندیکس');
    $hasDemo = $template->demoFile() !== null;
    $totalSize = $template->files->sum('size');
@endphp

@once
    <style>
        .rx-tem-hero {
            position: relative;
            padding: 130px 0 40px;
            overflow: hidden
        }

        .rx-tem-hero::before {
            content: '';
            position: absolute;
            top: -100px;
            inset-inline-start: 10%;
            width: 520px;
            height: 520px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(69, 200, 255, .16), transparent 65%);
            filter: blur(60px);
            pointer-events: none
        }

        .rx-tem-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 18px;
            font-size: 12.5px;
            font-weight: 850;
            color: var(--rx-fg-soft)
        }

        .rx-tem-breadcrumb a {
            color: var(--rx-fg-muted);
            text-decoration: none;
            transition: color .2s
        }

        .rx-tem-breadcrumb a:hover {
            color: var(--rx-accent-text)
        }

        .rx-tem-breadcrumb i {
            font-size: 9px;
            opacity: .6
        }

        .rx-tem-breadcrumb strong {
            color: var(--rx-fg)
        }

        .rx-tem-head {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, .9fr);
            gap: 40px;
            align-items: start;
            margin-bottom: 40px
        }

        .rx-tem-meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px
        }

        .rx-tem-type {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 950;
            color: #fff;
            background: linear-gradient(90deg, var(--rx-accent-2), var(--rx-accent));
            box-shadow: 0 10px 26px -14px rgba(23, 105, 255, .9)
        }

        .rx-tem-type.frontend {
            background: linear-gradient(90deg, #7653F6, #45C8FF);
            box-shadow: 0 10px 26px -14px rgba(118, 83, 246, .9)
        }

        .rx-tem-title {
            margin: 0;
            font-size: clamp(30px, 4.6vw, 50px);
            line-height: 1.22;
            font-weight: 950;
            letter-spacing: -.02em
        }

        .rx-tem-subtitle {
            margin: 16px 0 0;
            color: var(--rx-fg-muted);
            font-size: 15.5px;
            line-height: 2.1;
            max-width: 560px
        }

        .rx-tem-stats {
            display: flex;
            gap: 10px;
            margin-top: 22px;
            flex-wrap: wrap
        }

        .rx-tem-stat {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: var(--rx-panel-2);
            border: 1px solid var(--rx-border);
            font-size: 12px;
            font-weight: 850;
            color: var(--rx-fg-muted)
        }

        .rx-tem-stat i {
            color: var(--rx-accent-text)
        }

        .rx-lang-pill {
            display: inline-flex;
            align-items: center;
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 950;
            color: var(--rx-fg);
            border: 1px solid var(--rx-border-strong)
        }

        .rx-tem-buy-card {
            position: sticky;
            top: 100px;
            padding: 28px;
            border-radius: 28px;
            background: linear-gradient(160deg, var(--rx-panel), var(--rx-panel-2));
            border: 1px solid var(--rx-border);
            box-shadow: 0 30px 80px -38px var(--rx-shadow);
            backdrop-filter: blur(14px)
        }

        .rx-tem-buy-head {
            padding-bottom: 20px;
            border-bottom: 1px solid var(--rx-border);
            margin-bottom: 20px
        }

        .rx-tem-buy-head .price-label {
            font-size: 12px;
            font-weight: 850;
            color: var(--rx-fg-soft)
        }

        .rx-tem-buy-head .price-value {
            font-size: clamp(28px, 3.2vw, 36px);
            font-weight: 950;
            background: linear-gradient(90deg, var(--rx-accent), var(--rx-accent-2) 55%, var(--rx-accent-3));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent
        }

        .rx-tem-buy-head .price-unit {
            font-size: 13px;
            font-weight: 850;
            color: var(--rx-fg-muted);
            margin-inline-start: 6px
        }

        .rx-tem-features {
            display: grid;
            gap: 10px;
            margin-bottom: 22px
        }

        .rx-tem-feat {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 14px;
            background: var(--rx-panel-2);
            border: 1px solid var(--rx-border);
            font-size: 13px;
            font-weight: 850
        }

        .rx-tem-feat .ico {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 14px
        }

        .rx-tem-feat.on .ico {
            color: #86efac;
            background: rgba(34, 197, 94, .12);
            border: 1px solid rgba(34, 197, 94, .24)
        }

        .rx-tem-feat.off .ico {
            color: var(--rx-fg-soft);
            background: rgba(255, 255, 255, .04);
            border: 1px solid var(--rx-border)
        }

        .rx-tem-feat.off {
            color: var(--rx-fg-soft);
            text-decoration: line-through
        }

        .rx-tem-buy-actions {
            display: grid;
            gap: 10px
        }

        .rx-tem-buy-main {
            width: 100%;
            min-height: 54px;
            border: 0;
            border-radius: 16px;
            color: #fff;
            font-size: 15px;
            font-weight: 950;
            cursor: pointer;
            background: linear-gradient(90deg, var(--rx-accent-2), var(--rx-accent));
            box-shadow: 0 18px 42px -18px rgba(23, 105, 255, .95);
            transition: all .25s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px
        }

        .rx-tem-buy-main:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 50px -18px rgba(69, 200, 255, .95)
        }

        .rx-tem-buy-demo {
            width: 100%;
            min-height: 48px;
            border-radius: 14px;
            color: var(--rx-fg);
            background: var(--rx-panel-2);
            border: 1px solid var(--rx-border-strong);
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            transition: all .22s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px
        }

        .rx-tem-buy-demo:hover {
            border-color: rgba(69, 200, 255, .45);
            background: rgba(69, 200, 255, .08);
            transform: translateY(-1px)
        }

        .rx-tem-buy-demo:disabled {
            opacity: .45;
            cursor: not-allowed;
            transform: none
        }

        .rx-tem-body {
            margin-top: 40px;
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(300px, .6fr);
            gap: 28px;
            align-items: start
        }

        .rx-tem-section {
            padding: 28px;
            border-radius: 26px;
            background: linear-gradient(135deg, var(--rx-panel), var(--rx-panel-2));
            border: 1px solid var(--rx-border);
            margin-bottom: 22px
        }

        .rx-tem-section h2 {
            margin: 0 0 18px;
            font-size: 20px;
            font-weight: 950;
            display: flex;
            align-items: center;
            gap: 10px
        }

        .rx-tem-section h2 i {
            color: var(--rx-accent-text);
            font-size: 18px
        }

        .rx-tem-desc {
            color: var(--rx-fg-muted);
            font-size: 15px;
            line-height: 2.3;
            white-space: pre-line
        }

        .rx-tem-feat-list {
            display: grid;
            gap: 10px
        }

        .rx-tem-feat-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            background: var(--rx-panel-2);
            border: 1px solid var(--rx-border)
        }

        .rx-tem-feat-item i {
            color: #86efac;
            margin-top: 4px;
            flex-shrink: 0
        }

        .rx-tem-feat-item span {
            color: var(--rx-fg);
            font-size: 14px;
            line-height: 1.9
        }

        .rx-tem-side {
            display: grid;
            gap: 18px
        }

        .rx-tem-info-card {
            padding: 22px;
            border-radius: 22px;
            background: linear-gradient(135deg, var(--rx-panel), var(--rx-panel-2));
            border: 1px solid var(--rx-border)
        }

        .rx-tem-info-card h3 {
            margin: 0 0 14px;
            font-size: 16px;
            font-weight: 950
        }

        .rx-tem-info-list {
            display: grid;
            gap: 10px
        }

        .rx-tem-info-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 11px 13px;
            border-radius: 12px;
            background: var(--rx-panel-2);
            border: 1px solid var(--rx-border)
        }

        .rx-tem-info-item span {
            color: var(--rx-fg-soft);
            font-size: 12px;
            font-weight: 850
        }

        .rx-tem-info-item strong {
            color: var(--rx-fg);
            font-size: 13px;
            font-weight: 950;
            text-align: left
        }

        .rx-tem-report {
            padding: 18px;
            border-radius: 18px;
            background: rgba(244, 63, 94, .05);
            border: 1px solid rgba(244, 63, 94, .15)
        }

        .rx-tem-report-btn {
            width: 100%;
            padding: 10px;
            border-radius: 12px;
            background: transparent;
            border: 1px solid rgba(244, 63, 94, .25);
            color: #fda4af;
            font-size: 13px;
            font-weight: 850;
            cursor: pointer;
            transition: all .22s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px
        }

        .rx-tem-report-btn:hover {
            background: rgba(244, 63, 94, .1);
            border-color: rgba(244, 63, 94, .45)
        }

        .rx-tem-report-form {
            margin-top: 14px;
            display: grid;
            gap: 10px
        }

        .rx-tem-report-form input,
        .rx-tem-report-form textarea {
            width: 100%;
            min-height: 42px;
            padding: 10px 13px;
            border-radius: 12px;
            background: var(--rx-panel-2);
            border: 1px solid var(--rx-border);
            color: var(--rx-fg);
            outline: none;
            font-size: 13px;
            font-family: inherit
        }

        .rx-tem-report-form textarea {
            min-height: 90px;
            resize: vertical;
            line-height: 1.9
        }

        .rx-tem-report-form input:focus,
        .rx-tem-report-form textarea:focus {
            border-color: rgba(69, 200, 255, .5);
            box-shadow: 0 0 0 3px rgba(69, 200, 255, .1)
        }

        .rx-tem-report-form button {
            padding: 10px;
            border-radius: 12px;
            border: 0;
            background: linear-gradient(90deg, #1769FF, #45C8FF);
            color: #fff;
            font-weight: 900;
            cursor: pointer
        }

        .rx-tem-report-success {
            padding: 12px;
            border-radius: 12px;
            background: rgba(34, 197, 94, .1);
            border: 1px solid rgba(34, 197, 94, .25);
            color: #bbf7d0;
            font-size: 13px;
            font-weight: 850;
            text-align: center
        }

        .rx-tem-preview-overlay {
            position: fixed;
            inset: 0;
            z-index: 2147483600;
            background: rgba(7, 19, 41, .85);
            backdrop-filter: blur(14px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px
        }

        .rx-tem-preview-overlay.is-open {
            display: flex;
            animation: rxTemIn .25s ease
        }

        .rx-tem-preview-frame {
            position: relative;
            width: min(1400px, 100%);
            height: min(90vh, 900px);
            border-radius: 22px;
            overflow: hidden;
            background: #000;
            border: 1px solid rgba(69, 200, 255, .28);
            box-shadow: 0 50px 140px -40px rgba(0, 0, 0, .95);
            display: flex;
            flex-direction: column
        }

        .rx-tem-preview-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 12px 16px;
            background: linear-gradient(90deg, rgba(23, 105, 255, .14), rgba(118, 83, 246, .10));
            border-bottom: 1px solid rgba(255, 255, 255, .08);
            flex-shrink: 0
        }

        .rx-tem-preview-top .title {
            color: #F7F9FF;
            font-size: 13px;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 10px
        }

        .rx-tem-preview-top .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #22c55e;
            animation: rxTemPulse 1.8s ease-in-out infinite
        }

        .rx-tem-preview-actions {
            display: flex;
            gap: 6px
        }

        .rx-tem-preview-actions button {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            border: 1px solid rgba(255, 255, 255, .10);
            background: rgba(255, 255, 255, .05);
            color: #F7F9FF;
            cursor: pointer;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            justify-content: center
        }

        .rx-tem-preview-actions button:hover {
            background: rgba(69, 200, 255, .12);
            border-color: rgba(69, 200, 255, .3)
        }

        .rx-tem-preview-actions .close:hover {
            background: rgba(244, 63, 94, .18);
            border-color: rgba(244, 63, 94, .35);
            color: #fecaca
        }

        .rx-tem-preview-iframe {
            width: 100%;
            flex: 1;
            border: 0;
            background: #fff
        }

        @keyframes rxTemIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        @keyframes rxTemPulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, .4)
            }

            50% {
                box-shadow: 0 0 0 8px rgba(34, 197, 94, 0)
            }
        }

        @media(max-width:1100px) {
            .rx-tem-head {
                grid-template-columns: 1fr;
                gap: 28px
            }

            .rx-tem-buy-card {
                position: static
            }

            .rx-tem-body {
                grid-template-columns: 1fr
            }
        }

        @media(max-width:720px) {
            .rx-tem-hero {
                padding: 110px 0 30px
            }

            .rx-tem-preview-frame {
                height: 88vh;
                border-radius: 16px
            }
        }
    </style>
@endonce

@section('rendix.title', $template->name . ' | فروشگاه قالب ' . $siteName)
@section('rendix.description', \Illuminate\Support\Str::limit($template->description ?: 'قالب برنامه‌نویسی حرفه‌ای رندیکس', 160))
@section('rendix.canonical', route('templates.show', $template->slug))

<div>
    <section class="rx-tem-hero">
        <div class="rx-container">
            <nav class="rx-tem-breadcrumb">
                <a href="{{ route('home') }}" wire:navigate>خانه</a>
                <i class="fa-solid fa-chevron-left"></i>
                <a href="{{ route('templates.index') }}" wire:navigate>فروشگاه قالب</a>
                <i class="fa-solid fa-chevron-left"></i>
                <strong>{{ \Illuminate\Support\Str::limit($template->name, 40) }}</strong>
            </nav>

            <div class="rx-tem-head">
                <div>
                    <div class="rx-tem-meta-row">
                        <span class="rx-tem-type {{ $template->is_fullstack ? '' : 'frontend' }}">
                            <i class="fa-solid {{ $template->is_fullstack ? 'fa-layer-group' : 'fa-palette' }}"></i>
                            {{ $template->is_fullstack ? 'Full-Stack (با بک‌اند)' : 'فقط Front-End' }}
                        </span>
                        @foreach ($template->languages as $lng)
                            <span class="rx-lang-pill"
                                style="background:{{ $lng->color ?? 'rgba(69,200,255,.12)' }};">{{ $lng->language }}</span>
                        @endforeach
                    </div>

                    <h1 class="rx-tem-title">{{ $template->name }}</h1>

                    @if ($template->description)
                        <p class="rx-tem-subtitle">{{ \Illuminate\Support\Str::limit($template->description, 260) }}</p>
                    @endif

                    <div class="rx-tem-stats">
                        <span class="rx-tem-stat"><i class="fa-regular fa-eye"></i>
                            {{ \App\Support\Jalali::toPersianDigits(number_format($template->views_count)) }}
                            بازدید</span>
                        <span class="rx-tem-stat"><i class="fa-solid fa-download"></i>
                            {{ \App\Support\Jalali::toPersianDigits(number_format($template->downloads_count)) }}
                            فروش</span>
                        <span class="rx-tem-stat"><i class="fa-solid fa-file-zipper"></i>
                            {{ \App\Support\Jalali::toPersianDigits((string) $template->files->count()) }} فایل</span>
                    </div>
                </div>

                <aside class="rx-tem-buy-card">
                    <div class="rx-tem-buy-head">
                        <div class="price-label">قیمت قالب</div>
                        <div class="price-value">{{ number_format($template->price, 0, '.', ',') }}<span
                                class="price-unit">تومان</span></div>
                    </div>

                    <div class="rx-tem-features">
                        <div class="rx-tem-feat {{ $template->has_support ? 'on' : 'off' }}"><span class="ico"><i
                                    class="fa-solid {{ $template->has_support ? 'fa-check' : 'fa-xmark' }}"></i></span><span>پشتیبانی
                                فنی</span></div>
                        <div class="rx-tem-feat {{ $template->has_trial ? 'on' : 'off' }}"><span class="ico"><i
                                    class="fa-solid {{ $template->has_trial ? 'fa-check' : 'fa-xmark' }}"></i></span><span>مهلت
                                تست</span></div>
                        <div class="rx-tem-feat {{ $template->has_guarantee ? 'on' : 'off' }}"><span class="ico"><i
                                    class="fa-solid {{ $template->has_guarantee ? 'fa-check' : 'fa-xmark' }}"></i></span><span>ضمانت
                                بازگشت وجه</span></div>
                    </div>

                    <div class="rx-tem-buy-actions">
                        <button type="button" class="rx-tem-buy-main"
                            onclick="window.rxToast && rxToast({type:'info', message:'درگاه پرداخت به‌زودی فعال می‌شود؛ فعلاً از طریق تلگرام سفارش بده.'})">
                            <i class="fa-solid fa-cart-shopping"></i><span>خرید و دانلود</span>
                        </button>

                        <button type="button" class="rx-tem-buy-demo" @if ($hasDemo)
                            onclick="document.getElementById('rx-tem-preview').classList.add('is-open'); document.getElementById('rx-tem-preview-iframe').src='{{ route('templates.preview', $template) }}'; document.body.style.overflow='hidden';"
                        @else disabled @endif>
                            <i
                                class="fa-solid fa-play"></i><span>{{ $hasDemo ? 'مشاهده دمو زنده' : 'دمو موجود نیست' }}</span>
                        </button>
                    </div>
                </aside>
            </div>

            <div class="rx-tem-body">
                <div>
                    @if ($template->description)
                        <div class="rx-tem-section">
                            <h2><i class="fa-solid fa-file-lines"></i> توضیحات کامل</h2>
                            <div class="rx-tem-desc">{{ $template->description }}</div>
                        </div>
                    @endif

                    @if (!empty($template->features))
                        <div class="rx-tem-section">
                            <h2><i class="fa-solid fa-list-check"></i> امکانات قالب</h2>
                            <div class="rx-tem-feat-list">
                                @foreach ($template->features as $feature)
                                    <div class="rx-tem-feat-item"><i
                                            class="fa-solid fa-circle-check"></i><span>{{ $feature }}</span></div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($template->files->isNotEmpty())
                        <div class="rx-tem-section">
                            <h2><i class="fa-solid fa-folder-tree"></i> فایل‌های داخل پکیج</h2>
                            <div class="rx-tem-feat-list">
                                @foreach ($template->files as $file)
                                    <div class="rx-tem-feat-item">
                                        <i class="fa-solid {{ $file->is_demo ? 'fa-star' : 'fa-file' }}"></i>
                                        <span>{{ $file->filename }} <small
                                                style="display:block;color:var(--rx-fg-soft);font-size:11px;margin-top:3px;">{{ $file->humanSize() }}@if ($file->is_demo)
                                                — فایل دمو @endif</small></span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="rx-tem-side">
                    <div class="rx-tem-info-card">
                        <h3>اطلاعات فنی</h3>
                        <div class="rx-tem-info-list">
                            <div class="rx-tem-info-item">
                                <span>نوع</span><strong>{{ $template->is_fullstack ? 'Full-Stack' : 'Front-End' }}</strong>
                            </div>
                            <div class="rx-tem-info-item">
                                <span>زبان‌ها</span><strong>{{ $template->languages->pluck('language')->implode('، ') ?: '—' }}</strong>
                            </div>
                            <div class="rx-tem-info-item"><span>حجم پکیج</span><strong
                                    dir="ltr">{{ $totalSize > 0 ? round($totalSize / 1048576, 2) . ' MB' : '—' }}</strong>
                            </div>
                            <div class="rx-tem-info-item">
                                <span>انتشار</span><strong>{{ \App\Support\Jalali::date($template->created_at) }}</strong>
                            </div>
                            <div class="rx-tem-info-item"><span>آخرین
                                    به‌روزرسانی</span><strong>{{ \App\Support\Jalali::date($template->updated_at) }}</strong>
                            </div>
                        </div>
                    </div>

                    <livewire:templates.report-form :template-id="$template->id" />
                </div>
            </div>
        </div>
    </section>

    @if ($hasDemo)
        <div class="rx-tem-preview-overlay" id="rx-tem-preview">
            <div class="rx-tem-preview-frame">
                <div class="rx-tem-preview-top">
                    <div class="title"><span class="live-dot"></span><span>دمو زنده: {{ $template->name }}</span></div>
                    <div class="rx-tem-preview-actions">
                        <button type="button" title="تب جدید"
                            onclick="window.open(document.getElementById('rx-tem-preview-iframe').src,'_blank')"><i
                                class="fa-solid fa-up-right-from-square"></i></button>
                        <button type="button" title="بارگذاری مجدد"
                            onclick="var f=document.getElementById('rx-tem-preview-iframe'); f.src=f.src;"><i
                                class="fa-solid fa-rotate"></i></button>
                        <button type="button" class="close" title="بستن"
                            onclick="document.getElementById('rx-tem-preview').classList.remove('is-open'); document.getElementById('rx-tem-preview-iframe').src=''; document.body.style.overflow='';"><i
                                class="fa-solid fa-xmark"></i></button>
                    </div>
                </div>
                <iframe id="rx-tem-preview-iframe" class="rx-tem-preview-iframe" sandbox="allow-scripts allow-same-origin"
                    title="دمو زنده قالب"></iframe>
            </div>
        </div>
    @endif
</div>