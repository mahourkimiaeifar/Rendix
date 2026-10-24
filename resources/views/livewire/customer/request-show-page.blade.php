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
        $user = auth()->user();

        return ProjectRequest::query()
            ->with('notes.user')
            ->where('tracking_code', $this->code)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function ($q) use ($user) {
                          $q->whereNull('user_id')
                            ->where('email', $user->email);
                      });
            })
            ->first();
    }
};

?>

@php
    $request = $this->request();

    if (! $request) {
        abort(404);
    }

    $siteName = setting('site.name', 'رندیکس');
    $telegram = setting('social.telegram', 'https://t.me/RendixDMServices');

    $statusFlow = [
        'new' => [
            'title' => 'درخواست دریافت شد',
            'desc' => 'درخواست شما در سیستم ثبت شد و در صف بررسی قرار دارد.',
            'icon' => 'fa-solid fa-inbox',
        ],
        'reviewing' => [
            'title' => 'در حال بررسی',
            'desc' => 'تیم ' . $siteName . ' در حال بررسی جزئیات پروژه و نیازهای شماست.',
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
            'desc' => 'پروژه وارد فاز اجرا شده و تیم ' . $siteName . ' در حال کار است.',
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

            <a href="{{ route('customer.requests') }}" class="inline-flex items-center gap-2 text-sm font-bold text-snow/55 hover:text-sky transition-colors mb-8">
                <i class="fa-solid fa-arrow-right"></i>
                بازگشت به درخواست‌ها
            </a>

            <div class="glass p-7 md:p-10 space-y-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                    <div class="space-y-3">
                        <div class="badge">
                            <span class="badge-dot"></span>
                            جزئیات درخواست پروژه
                        </div>

                        <h1 class="text-3xl md:text-5xl font-black leading-[1.25]">
                            {{ $request->service }}
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
                        <h3 class="text-base font-black text-sky">اطلاعات ثبت‌شده</h3>

                        <div class="space-y-2 text-sm leading-7">
                            <p><span class="text-snow/45">نام:</span> {{ $request->name }}</p>
                            <p><span class="text-snow/45">تلفن:</span> <span dir="ltr">{{ $request->phone }}</span></p>
                            @if ($request->email)
                                <p><span class="text-snow/45">ایمیل:</span> <span dir="ltr">{{ $request->email }}</span></p>
                            @endif
                            @if ($request->company)
                                <p><span class="text-snow/45">شرکت:</span> {{ $request->company }}</p>
                            @endif
                            <p><span class="text-snow/45">تاریخ ثبت:</span> {{ Jalali::date($request->created_at) }}</p>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-3">
                        <h3 class="text-base font-black text-sky">مشخصات پروژه</h3>

                        <div class="space-y-2 text-sm leading-7">
                            <p><span class="text-snow/45">خدمت:</span> {{ $request->service }}</p>
                            <p><span class="text-snow/45">وبسایت فعلی:</span> {{ $request->websiteStatusLabel() }}</p>
                            @if ($request->current_website_url)
                                <p><span class="text-snow/45">آدرس سایت:</span> <span dir="ltr">{{ $request->current_website_url }}</span></p>
                            @endif
                            <p><span class="text-snow/45">زمان‌بندی:</span> {{ $request->timelineLabel() }}</p>
                            <p><span class="text-snow/45">بودجه:</span> {{ $request->budgetLabel() }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/5 border border-white/10 p-6 space-y-5">
                    <h3 class="text-lg font-black">توضیحات شما</h3>

                    <div class="space-y-4 text-sm leading-8 text-snow/70">
                        @if ($request->business_description)
                            <div>
                                <div class="text-snow/45 font-bold mb-1">درباره کسب‌وکار</div>
                                <div class="whitespace-pre-line">{{ $request->business_description }}</div>
                            </div>
                        @endif

                        @if ($request->project_goals)
                            <div>
                                <div class="text-snow/45 font-bold mb-1">هدف پروژه</div>
                                <div class="whitespace-pre-line">{{ $request->project_goals }}</div>
                            </div>
                        @endif

                        @if ($request->competitors)
                            <div>
                                <div class="text-snow/45 font-bold mb-1">رقبا / نمونه‌ها</div>
                                <div class="whitespace-pre-line">{{ $request->competitors }}</div>
                            </div>
                        @endif

                        @if ($request->references)
                            <div>
                                <div class="text-snow/45 font-bold mb-1">فایل‌ها / محتوا</div>
                                <div class="whitespace-pre-line">{{ $request->references }}</div>
                            </div>
                        @endif

                        @if ($request->message)
                            <div>
                                <div class="text-snow/45 font-bold mb-1">توضیحات تکمیلی</div>
                                <div class="whitespace-pre-line">{{ $request->message }}</div>
                            </div>
                        @endif
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

                @if ($request->notes->isNotEmpty())
                    <div class="rounded-2xl bg-white/5 border border-white/10 p-6 space-y-5">
                        <h3 class="text-lg font-black">یادداشت‌های تیم</h3>

                        <div class="space-y-3">
                            @foreach ($request->notes->sortByDesc('created_at') as $note)
                                <div class="rounded-xl border border-white/10 bg-white/5 p-4 space-y-2">
                                    <div class="flex items-center justify-between gap-3 text-xs">
                                        <strong class="text-snow/75">{{ $note->user?->name ?? 'تیم ' . $siteName }}</strong>
                                        <span class="text-snow/45">{{ Jalali::date($note->created_at) }}</span>
                                    </div>

                                    <p class="text-sm text-snow/65 leading-7 whitespace-pre-line">
                                        {{ $note->body }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

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