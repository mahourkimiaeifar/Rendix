<?php

use App\Models\ProjectRequest;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public function requestsQuery()
    {
        $user = auth()->user();

        return ProjectRequest::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function ($q) use ($user) {
                          $q->whereNull('user_id')
                            ->where('email', $user->email);
                      });
            });
    }

    public function stats(): array
    {
        $base = $this->requestsQuery();

        return [
            'total' => (clone $base)->count(),
            'new' => (clone $base)->where('status', 'new')->count(),
            'in_progress' => (clone $base)->whereIn('status', ['reviewing', 'meeting_scheduled', 'proposal_sent', 'waiting_client', 'in_progress'])->count(),
            'delivered' => (clone $base)->where('status', 'delivered')->count(),
        ];
    }

    public function recentRequests()
    {
        return $this->requestsQuery()
            ->ordered()
            ->limit(5)
            ->get();
    }
};

?>

@php
    $user = auth()->user();
    $stats = $this->stats();
    $recent = $this->recentRequests();
    $siteName = setting('site.name', 'رندیکس');
@endphp

<div>
    <section class="pt-36 pb-24 px-5 md:px-10">
        <div class="max-w-7xl mx-auto">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-12">
                <div class="space-y-3">
                    <div class="badge">
                        <span class="badge-dot"></span>
                        داشبورد مشتری {{ $siteName }}
                    </div>

                    <h1 class="text-3xl md:text-5xl font-black leading-[1.25]">
                        سلام {{ $user->name }} 👋
                    </h1>

                    <p class="text-snow/60 leading-8">
                        از اینجا می‌توانی درخواست‌های پروژه، کدهای پیگیری و وضعیت همکاری‌هایت را مدیریت کنی.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('request-project') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i>
                        درخواست پروژه جدید
                    </a>

                    <a href="{{ route('customer.profile') }}" class="btn btn-primary">
                        <i class="fa-solid fa-user-gear"></i>
                        پروفایل
                    </a>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
                <div class="glass p-6 space-y-2">
                    <div class="text-xs text-snow/45 font-bold">کل درخواست‌ها</div>
                    <div class="text-3xl font-black text-sky">
                        {{ Jalali::toPersianDigits(number_format($stats['total'])) }}
                    </div>
                </div>

                <div class="glass p-6 space-y-2">
                    <div class="text-xs text-snow/45 font-bold">دریافت شده</div>
                    <div class="text-3xl font-black text-amber-300">
                        {{ Jalali::toPersianDigits(number_format($stats['new'])) }}
                    </div>
                </div>

                <div class="glass p-6 space-y-2">
                    <div class="text-xs text-snow/45 font-bold">در جریان</div>
                    <div class="text-3xl font-black text-emerald-300">
                        {{ Jalali::toPersianDigits(number_format($stats['in_progress'])) }}
                    </div>
                </div>

                <div class="glass p-6 space-y-2">
                    <div class="text-xs text-snow/45 font-bold">تحویل شده</div>
                    <div class="text-3xl font-black text-sky">
                        {{ Jalali::toPersianDigits(number_format($stats['delivered'])) }}
                    </div>
                </div>
            </div>

            <div class="glass p-6 md:p-8 space-y-6">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-2xl font-black">آخرین درخواست‌ها</h2>

                    <a href="{{ route('customer.requests') }}" class="text-sm font-bold text-sky hover:text-sky/80 transition-colors">
                        مشاهده همه
                    </a>
                </div>

                @if ($recent->isEmpty())
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-8 text-center space-y-4">
                        <div class="text-5xl text-sky/60">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>

                        <h3 class="text-xl font-black">هنوز درخواستی ثبت نکرده‌ای</h3>

                        <p class="text-sm text-snow/55 leading-7">
                            اولین درخواست پروژه خودت را ثبت کن تا کد پیگیری بگیری و وضعیتش را اینجا دنبال کنی.
                        </p>

                        <a href="{{ route('request-project') }}" class="btn btn-primary">
                            ثبت درخواست پروژه
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($recent as $request)
                            <a
                                href="{{ route('customer.requests.show', $request->tracking_code) }}"
                                class="block rounded-2xl border border-white/10 bg-white/5 p-5 hover:border-sky/30 transition-all"
                            >
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div class="space-y-2">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <strong dir="ltr" class="text-sky font-black">
                                                {{ $request->tracking_code }}
                                            </strong>

                                            <span class="text-[11px] font-black rounded-full px-3 py-1 border
                                                {{ $request->status === 'delivered' ? 'text-emerald-300 border-emerald-400/25 bg-emerald-400/10' : ($request->status === 'cancelled' || $request->status === 'archived' ? 'text-rose-300 border-rose-400/25 bg-rose-400/10' : 'text-sky border-sky/25 bg-sky/10') }}">
                                                {{ $request->statusLabel() }}
                                            </span>
                                        </div>

                                        <div class="text-sm text-snow/65 leading-7">
                                            خدمت: {{ $request->service }}
                                            —
                                            بودجه: {{ $request->budgetLabel() }}
                                        </div>
                                    </div>

                                    <div class="text-xs text-snow/45 font-bold md:text-left">
                                        {{ Jalali::date($request->created_at) }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>