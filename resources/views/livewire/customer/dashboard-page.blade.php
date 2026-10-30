<?php

use App\Models\ProjectRequest;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.customer')]
    class extends Component {
    public function requestsQuery()
    {
        $user = auth()->user();

        return ProjectRequest::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere(function ($q) use ($user) {
                        $q->whereNull('user_id')->where('email', $user->email);
                    });
            });
    }

    public function stats(): array
    {
        $base = $this->requestsQuery();

        return [
            'total' => (clone $base)->count(),
            'new' => (clone $base)->where('status', 'new')->count(),
            'active' => (clone $base)->whereIn('status', ['reviewing', 'meeting_scheduled', 'proposal_sent', 'waiting_client', 'in_progress'])->count(),
            'done' => (clone $base)->where('status', 'delivered')->count(),
        ];
    }

    public function recentRequests()
    {
        return $this->requestsQuery()->ordered()->limit(5)->get();
    }
};

?>

@php
    $user = auth()->user();
    $stats = $this->stats();
    $recent = $this->recentRequests();
    $siteName = setting('site.name', 'رندیکس');

    $statusClass = function (string $status): string {
        return match (true) {
            $status === 'delivered' => 'st-done',
            in_array($status, ['cancelled', 'archived'], true) => 'st-cancel',
            in_array($status, ['waiting_client', 'proposal_sent'], true) => 'st-wait',
            $status === 'new' => 'st-new',
            default => 'st-progress',
        };
    };
@endphp

@section('rendix.title', 'داشبورد | ' . $siteName)
<div>
    <div class="max-w-3xl space-y-5 mb-8">
        <div class="badge fade-up">
            <span class="badge-dot"></span>
            داشبورد مشتری {{ $siteName }}
        </div>

        <h1 class="text-3xl md:text-5xl font-black leading-[1.25] fade-up delay-1">
            سلام {{ $user->name }} 👋
        </h1>

        <p class="text-snow/60 leading-8 fade-up delay-2">
            از اینجا می‌توانی درخواست‌های پروژه، کدهای پیگیری و وضعیت همکاری‌هایت را مدیریت کنی.
        </p>

        <div class="flex flex-wrap gap-3 fade-up delay-3">
            <a href="{{ route('request-project') }}" class="btn btn-primary btn-sm" wire:navigate>
                <i class="fa-solid fa-rocket"></i>
                <span>درخواست پروژه جدید</span>
            </a>

            <a href="{{ route('customer.profile') }}" class="btn btn-ghost btn-sm" wire:navigate>
                <i class="fa-solid fa-user-gear"></i>
                <span>پروفایل</span>
            </a>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="glass glass-hover p-5 space-y-2 rv">
            <div class="flex items-center justify-between gap-3">
                <span class="text-xs text-snow/45 font-bold">کل درخواست‌ها</span>
                <div class="icon-tile" style="width:42px;height:42px;font-size:16px;"><i class="fa-solid fa-inbox"></i>
                </div>
            </div>
            <strong
                class="block text-3xl font-black text-sky">{{ Jalali::toPersianDigits((string) $stats['total']) }}</strong>
            <small class="block text-[11px] text-snow/40">بدون احتساب بایگانی‌شده‌ها</small>
        </div>

        <div class="glass glass-hover p-5 space-y-2 rv">
            <div class="flex items-center justify-between gap-3">
                <span class="text-xs text-snow/45 font-bold">دریافت شده</span>
                <div class="icon-tile" style="width:42px;height:42px;font-size:16px;"><i
                        class="fa-solid fa-envelope-open-text"></i></div>
            </div>
            <strong
                class="block text-3xl font-black text-sky">{{ Jalali::toPersianDigits((string) $stats['new']) }}</strong>
            <small class="block text-[11px] text-snow/40">هنوز پیگیری نشده‌اند</small>
        </div>

        <div class="glass glass-hover p-5 space-y-2 rv">
            <div class="flex items-center justify-between gap-3">
                <span class="text-xs text-snow/45 font-bold">در جریان</span>
                <div class="icon-tile icon-tile-gold" style="width:42px;height:42px;font-size:16px;"><i
                        class="fa-solid fa-hourglass-half"></i></div>
            </div>
            <strong
                class="block text-3xl font-black text-gold">{{ Jalali::toPersianDigits((string) $stats['active']) }}</strong>
            <small class="block text-[11px] text-snow/40">زمان پیگیری رسیده است</small>
        </div>

        <div class="glass glass-hover p-5 space-y-2 rv">
            <div class="flex items-center justify-between gap-3">
                <span class="text-xs text-snow/45 font-bold">تحویل شده</span>
                <div class="icon-tile icon-tile-mint" style="width:42px;height:42px;font-size:16px;"><i
                        class="fa-solid fa-circle-check"></i></div>
            </div>
            <strong
                class="block text-3xl font-black text-mint">{{ Jalali::toPersianDigits((string) $stats['done']) }}</strong>
            <small class="block text-[11px] text-snow/40">پروژه‌های تمام‌شده</small>
        </div>
    </div>

    <div class="glass p-6 md:p-7 space-y-5 rv">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-black">آخرین درخواست‌ها</h2>

            <a href="{{ route('customer.requests') }}"
                class="text-sm font-bold text-sky hover:text-sky/80 transition-colors" wire:navigate>
                مشاهده همه
            </a>
        </div>

        @if ($recent->isEmpty())
            <x-site.empty-state icon="fa-solid fa-clipboard-list" title="هنوز درخواستی ثبت نکرده‌ای"
                text="اولین درخواست پروژه خودت را ثبت کن تا کد پیگیری بگیری و وضعیتش را اینجا دنبال کنی.">
                <a href="{{ route('request-project') }}" class="btn btn-primary btn-sm" style="margin-top:14px;"
                    wire:navigate>
                    <i class="fa-solid fa-rocket"></i>
                    <span>ثبت درخواست پروژه</span>
                </a>
            </x-site.empty-state>
        @else
            <div class="space-y-3">
                @foreach ($recent as $request)
                    <div class="rx-cust-row">
                        <div style="display:grid;gap:4px;min-width:0;">
                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                <span class="rx-cust-code" dir="ltr">{{ $request->tracking_code }}</span>
                                <span class="st-pill {{ $statusClass($request->status) }}">{{ $request->statusLabel() }}</span>
                            </div>
                            <span class="rx-cust-muted">
                                خدمت: {{ $request->service }}
                                —
                                بودجه: {{ $request->budgetLabel() }}
                            </span>
                        </div>

                        <div style="display:flex;align-items:center;gap:9px;flex-wrap:wrap;">
                            <span class="rx-cust-muted">{{ Jalali::date($request->created_at) }}</span>

                            <a href="{{ route('customer.requests.show', $request->tracking_code) }}"
                                class="btn btn-ghost btn-sm" wire:navigate>
                                جزئیات
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>