<?php

use App\Models\ProjectRequest;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $code = '';

    public function mount(string $code): void
    {
        $this->code = trim($code);
    }

    public function request(): ?ProjectRequest
    {
        return Project::query()
            ->where('tracking_code', $this->code)
            ->first();
    }
};

?>

@php
    $request = ProjectRequest::query()
        ->where('tracking_code', $this->code)
        ->first();

    if (! $request) {
        abort(404);
    }

    $siteName = setting('site.name', 'رندیکس');
    $telegram = setting('social.telegram', 'https://t.me/RendixDMServices');
    $contactPhone = setting('contact.phone');
    $contactPhoneTel = preg_replace('/\D/', '', (string) $contactPhone);

    $statusFlow = [
        'new' => [
            'title' => 'درخواست دریافت شد',
            'desc' => 'درخواست شما در سیستم ثبت شد و در صف بررسی قرار دارد.',
            'icon' => 'fa-solid fa-inbox',
        ],
        'reviewing' => [
            'title' => 'در حال بررسی',
            'desc' => 'تیم رندیکس در حال بررسی جزئیات پروژه و نیازهای شماست.',
            'icon' => 'fa-solid fa-magnifying-glass-chart',
        ],
        'meeting_scheduled' => [
            'title' => 'جلسه تعیین شد',
            'desc' => 'برای گفتگوی دقیق‌تر، زمان جلسه تعیین شده است.',
            'icon' => 'fa-solid fa-calendar-check',
        ],
        'proposal_sent' => [
            'title' => 'پیشنهاد ارسال شد',
            'desc' => 'پیشنهاد همکاری، بازه زمانی و برآورد هزینه برای شما ارسال شده است.',
            'icon' => 'fa-solid fa-file-signature',
        ],
        'waiting_client' => [
            'title' => 'در انتظار تأیید شما',
            'desc' => 'منتظر نظر نهایی شما برای شروع همکاری هستیم.',
            'icon' => 'fa-solid fa-hourglass-half',
        ],
        'in_progress' => [
            'title' => 'در حال اجرا',
            'desc' => 'پروژه وارد فاز اجرا شده و تیم رندیکس در حال کار است.',
            'icon' => 'fa-solid fa-rocket',
        ],
        'delivered' => [
            'title' => 'تحویل شد',
            'desc' => 'پروژه تحویل داده شد. پشتیبانی و مراحل بعدی طبق توافق انجام می‌شود.',
            'icon' => 'fa-solid fa-circle-check',
        ],
        'cancelled' => [
            'title' => 'لغو شد',
            'desc' => 'این درخواست لغو شده است. برای شروع مجدد با ما تماس بگیرید.',
            'icon' => 'fa-solid fa-xmark',
        ],
        'archived' => [
            'title' => 'بایگانی شد',
            'desc' => 'این درخواست بایگانی شده است.',
            'icon' => 'fa-solid fa-box-archive',
        ],
    ];

    $currentIndex = array_search($request->status, array_keys($statusFlow), true);

    if ($currentIndex === false) {
        $currentIndex = 0;
    }
@endphp

<div>
    <section class="pt-36 pb-24 px-5 md:px-10">
        <div class="max-w-5xl mx-auto">

            <a href="{{ route('request-project') }}" class="inline-flex items-center gap-2 text-sm font-bold text-snow/55 hover:text-sky transition-colors mb-8">
                <i class="fa-solid fa-arrow-right"></i>
                ثبت درخواست جدید
            </a>

            <div class="glass p-7 md:p-10 space-y-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                    <div class="space-y-3">
                        <div class="badge">
                            <span class="badge-dot"></span>
                            پیگیری درخواست پروژه
                        </div>

                        <h1 class="text-3xl md:text-5xl font-black leading-[1.25]">
                            وضعیت درخواست شما
                        </h1>

                        <p class="text-sm text-snow/55 leading-7">
                            کد پیگیری:
                            <strong dir="ltr" class="text-sky">{{ $request->tracking_code }}</strong>
                        </p>
                    </div>

                    <div class="rounded-2xl bg-white/5 border border-white/10 px-5 py-4 text-center min-w-44">
                        <div class="text-xs text-snow/45 font-bold mb-1">وضعیت فعلی</div>
                        <div class="text-lg font-black text-sky">
                            {{ $request->statusLabel() }}
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-3">
                        <h3 class="text-base font-black text-sky">اطلاعات مشتری</h3>

                        <div class="space-y-2 text-sm leading-7">
                            <p><span class="text-snow/45">نام:</span> {{ $request->name }}</p>
                            <p><span class="text-snow/45">تلفن:</span> <span dir="ltr">{{ $request->phone }}</span></p>
                            @if ($request->email)
                                <p><span class="text-snow/45">ایمیل:</span> <span dir="ltr">{{ $request->email }}</span></p>
                            @endif
                            @if ($request->company)
                                <p><span class="text-snow/45">شرکت:</span> {{ $request->company }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-3">
                        <h3 class="text-base font-black text-sky">اطلاعات پروژه</h3>

                        <div class="space-y-2 text-sm leading-7">
                            <p><span class="text-snow/45">خدمت:</span> {{ $request->service }}</p>
                            <p><span class="text-snow/45">زمان‌بندی:</span> {{ $request->timelineLabel() }}</p>
                            <p><span class="text-snow/45">بودجه:</span> {{ $request->budgetLabel() }}</p>
                            <p><span class="text-snow/45">تاریخ ثبت:</span> {{ Jalali::date($request->created_at) }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    <h2 class="text-xl font-black">مسیر درخواست</h2>

                    <div class="relative space-y-4 before:absolute before:top-4 before:bottom-4 before:right-[19px] before:w-px before:bg-white/10">
                        @foreach ($statusFlow as $status => $item)
                            @php
                                $index = array_search($status, array_keys($statusFlow), true);
                                $isDone = $index < $currentIndex;
                                $isCurrent = $index === $currentIndex;
                                $isFuture = $index > $currentIndex;
                            @endphp

                            <div class="relative flex gap-4 items-start">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center border z-10
                                    {{ $isCurrent ? 'border-sky bg-sky/15 text-sky' : ($isDone ? 'border-emerald-400/30 bg-emerald-400/10 text-emerald-300' : 'border-white/10 bg-white/5 text-snow/35') }}"
                                >
                                    @if ($isDone)
                                        <i class="fa-solid fa-check text-sm"></i>
                                    @else
                                        <i class="{{ $item['icon'] }} text-sm"></i>
                                    @endif
                                </div>

                                <div class="flex-1 rounded-2xl border p-4 transition-all
                                    {{ $isCurrent ? 'border-sky/35 bg-sky/10' : ($isDone ? 'border-emerald-400/20 bg-emerald-400/5' : 'border-white/10 bg-white/5') }}"
                                >
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-sm md:text-base font-black">
                                            {{ $item['title'] }}
                                        </h3>

                                        @if ($isCurrent)
                                            <span class="text-[11px] font-black text-sky bg-sky/10 border border-sky/20 rounded-full px-3 py-1">
                                                فعلی
                                            </span>
                                        @elseif ($isDone)
                                            <span class="text-[11px] font-black text-emerald-300 bg-emerald-400/10 border border-emerald-400/20 rounded-full px-3 py-1">
                                                انجام شد
                                            </span>
                                        @endif
                                    </div>

                                    <p class="mt-2 text-xs md:text-sm text-snow/55 leading-7">
                                        {{ $item['desc'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl bg-white/5 border border-white/10 p-6 space-y-5">
                    <h3 class="text-lg font-black">ارتباط سریع</h3>

                    <p class="text-sm text-snow/55 leading-7">
                        اگر سوال داری یا می‌خواهی جزئیات درخواستت را تغییر بدهی، از این راه‌ها با {{ $siteName }} در تماس باش.
                    </p>

                    <div class="flex flex-wrap gap-3">
                        @if ($telegram)
                            <a href="{{ $telegram }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                                <i class="fa-brands fa-telegram"></i>
                                تلگرام
                            </a>
                        @endif

                        @if ($contactPhoneTel)
                            <a href="tel:{{ $contactPhoneTel }}" class="btn btn-primary">
                                <i class="fa-solid fa-phone"></i>
                                تماس
                            </a>
                        @endif

                        <a href="{{ route('contact') }}" class="btn btn-primary">
                            <i class="fa-solid fa-paper-plane"></i>
                            فرم تماس
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>