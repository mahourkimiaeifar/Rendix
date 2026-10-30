<?php

use App\Models\ProjectRequest;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.customer')] class extends Component {
    public string $code = '';

    public function mount(string $code): void
    {
        $this->code = trim($code);
    }

    public function request(): ?ProjectRequest
    {
        $user = auth()->user();

        return ProjectRequest::query()
            ->where('tracking_code', $this->code)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function ($q) use ($user) {
                          $q->whereNull('user_id')->where('email', $user->email);
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
    $telegram = setting('social.telegram');

    $flow = [
        'new' => ['title' => 'درخواست دریافت شد', 'icon' => 'fa-solid fa-inbox'],
        'reviewing' => ['title' => 'در حال بررسی', 'icon' => 'fa-solid fa-magnifying-glass-chart'],
        'meeting_scheduled' => ['title' => 'جلسه تعیین شد', 'icon' => 'fa-solid fa-calendar-check'],
        'proposal_sent' => ['title' => 'پیشنهاد ارسال شد', 'icon' => 'fa-solid fa-file-signature'],
        'waiting_client' => ['title' => 'در انتظار تأیید شما', 'icon' => 'fa-solid fa-hourglass-half'],
        'in_progress' => ['title' => 'در حال اجرا', 'icon' => 'fa-solid fa-rocket'],
        'delivered' => ['title' => 'تحویل شد', 'icon' => 'fa-solid fa-circle-check'],
    ];

    $cancelled = in_array($request->status, ['cancelled', 'archived'], true);

    $currentIndex = array_search($request->status, array_keys($flow), true);

    if ($cancelled) {
        $currentIndex = count($flow);
    }

    if ($currentIndex === false) {
        $currentIndex = 0;
    }
@endphp

@section('cust.title', 'جزئیات درخواست')
@section('cust.icon', 'fa-solid fa-file-lines')

<div class="rx-cust-card" style="padding:20px;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:14px;">
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        <span class="rx-cust-code" dir="ltr" style="font-size:16px;">{{ $request->tracking_code }}</span>
        <button
            type="button"
            class="btn btn-ghost btn-sm btn-icon"
            title="کپی کد"
            onclick="navigator.clipboard.writeText('{{ $request->tracking_code }}').then(function(){ window.rxToast && rxToast({ type:'success', message:'کد پیگیری کپی شد.' }); })"
        >
            <i class="fa-solid fa-copy"></i>
        </button>
        <span class="st-pill {{ $request->status === 'delivered' ? 'st-done' : ($cancelled ? 'st-cancel' : (in_array($request->status, ['waiting_client', 'proposal_sent']) ? 'st-wait' : ($request->status === 'new' ? 'st-new' : 'st-progress'))) }}">
            {{ $request->statusLabel() }}
        </span>
    </div>

    <span class="rx-cust-muted">ثبت: {{ Jalali::date($request->created_at) }}</span>
</div>

<div class="rx-cust-card" style="padding:20px;">
    <h2 style="margin:0 0 16px;font-size:16px;font-weight:950;">مسیر درخواست</h2>

    <div class="rx-cust-timeline">
        @foreach ($flow as $key => $step)
            @php
                $index = array_search($key, array_keys($flow), true);
                $isDone = $index < $currentIndex;
                $isCurrent = $index === $currentIndex && ! $cancelled;
            @endphp

            <div class="rx-tl-item {{ $isDone ? 'done' : ($isCurrent ? 'current' : '') }}">
                <span class="rx-tl-dot">
                    <i class="{{ $isDone ? 'fa-solid fa-check' : $step['icon'] }}"></i>
                </span>

                <div class="rx-tl-body">
                    <strong>{{ $step['title'] }}</strong>
                    <span>
                        @if ($isDone)
                            انجام شد
                        @elseif ($isCurrent)
                            مرحله فعلی
                        @else
                            در انتظار
                        @endif
                    </span>
                </div>
            </div>
        @endforeach

        @if ($cancelled)
            <div class="rx-tl-item cancel">
                <span class="rx-tl-dot"><i class="fa-solid fa-xmark"></i></span>
                <div class="rx-tl-body">
                    <strong>{{ $request->statusLabel() }}</strong>
                    <span>برای شروع دوباره با ما در تماس باش.</span>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="rx-cust-card" style="padding:20px;display:grid;gap:14px;">
    <h2 style="margin:0;font-size:16px;font-weight:950;">اطلاعات ثبت‌شده</h2>

    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;" class="rx-show-grid">
        <div class="rx-cust-row" style="padding:12px 14px;">
            <span class="rx-cust-muted">خدمت</span>
            <strong style="font-size:13px;">{{ $request->service }}</strong>
        </div>
        <div class="rx-cust-row" style="padding:12px 14px;">
            <span class="rx-cust-muted">بودجه</span>
            <strong style="font-size:13px;">{{ $request->budgetLabel() }}</strong>
        </div>
        <div class="rx-cust-row" style="padding:12px 14px;">
            <span class="rx-cust-muted">زمان‌بندی</span>
            <strong style="font-size:13px;">{{ $request->timelineLabel() }}</strong>
        </div>
        <div class="rx-cust-row" style="padding:12px 14px;">
            <span class="rx-cust-muted">وبسایت فعلی</span>
            <strong style="font-size:13px;">{{ $request->websiteStatusLabel() }}</strong>
        </div>
    </div>

    @if ($request->business_description)
        <div>
            <div class="rx-cust-muted" style="margin-bottom:6px;">درباره کسب‌وکار</div>
            <div class="rx-cust-row" style="padding:12px 14px;align-items:flex-start;">
                <p style="margin:0;font-size:13px;line-height:2;white-space:pre-line;">{{ $request->business_description }}</p>
            </div>
        </div>
    @endif

    @if ($request->project_goals)
        <div>
            <div class="rx-cust-muted" style="margin-bottom:6px;">هدف پروژه</div>
            <div class="rx-cust-row" style="padding:12px 14px;align-items:flex-start;">
                <p style="margin:0;font-size:13px;line-height:2;white-space:pre-line;">{{ $request->project_goals }}</p>
            </div>
        </div>
    @endif
</div>

@if ($request->attachments->isNotEmpty())
    <div class="rx-cust-card" style="padding:20px;display:grid;gap:10px;">
        <h2 style="margin:0;font-size:16px;font-weight:950;">فایل‌های ضمیمه</h2>

        @foreach ($request->attachments as $attachment)
            <div class="rx-cust-row">
                <div style="display:flex;align-items:center;gap:12px;min-width:0;">
                    <div class="icon-tile" style="width:40px;height:40px;font-size:15px;">
                        <i class="{{ $attachment->isImage() ? 'fa-solid fa-image' : 'fa-solid fa-file' }}"></i>
                    </div>
                    <div style="min-width:0;">
                        <strong style="display:block;font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $attachment->original_name }}</strong>
                        <span class="rx-cust-muted" dir="ltr">{{ $attachment->humanSize() }}</span>
                    </div>
                </div>

                <a
                    href="{{ route('customer.attachments.download', [$request->tracking_code, $attachment->id]) }}"
                    class="btn btn-ghost btn-sm"
                >
                    <i class="fa-solid fa-download"></i>
                    <span>دانلود</span>
                </a>
            </div>
        @endforeach
    </div>
@endif

<div class="rx-cust-card" style="padding:20px;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:14px;">
    <div>
        <strong style="font-size:14px;">سوال یا تغییری داری؟</strong>
        <p class="rx-cust-muted" style="margin:4px 0 0;">تیم {{ $siteName }} معمولاً ظرف ۲۴ ساعت پاسخ می‌دهد.</p>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        @if ($telegram)
            <a href="{{ $telegram }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">
                <i class="fa-brands fa-telegram"></i>
                <span>تلگرام</span>
            </a>
        @endif

        <a href="{{ route('contact') }}" class="btn btn-ghost btn-sm" wire:navigate>
            <i class="fa-solid fa-paper-plane"></i>
            <span>فرم تماس</span>
        </a>
    </div>
</div>

<style>
    @media (max-width: 760px) {
        .rx-show-grid { grid-template-columns: 1fr !important; }
    }
</style>