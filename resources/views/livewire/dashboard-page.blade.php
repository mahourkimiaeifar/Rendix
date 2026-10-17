<?php

use App\Models\Lead;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

#[Layout('components.layouts.admin')]
new class extends Component {
    public function getStatsProperty(): array
    {
        $today = Jalali::parse(Jalali::today());

        return [
            'new_leads' => Lead::query()
                ->active()
                ->new()
                ->count(),

            'today_leads' => $today
                ? Lead::query()
                    ->whereBetween('created_at', [
                        $today->copy()->startOfDay(),
                        $today->copy()->endOfDay(),
                    ])
                    ->count()
                : 0,

            'needs_follow_up' => Lead::query()
                ->needsFollowUp()
                ->count(),

            'total_leads' => Lead::query()
                ->active()
                ->count(),
        ];
    }

    public function getRecentLeadsProperty()
    {
        return Lead::query()
            ->latest()
            ->limit(8)
            ->get();
    }
};

?>

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>داشبورد رندیکس</h1>
            <p>نمای کهکشانی از سرنخ‌ها، پیگیری‌ها و وضعیت رشد مشتری‌ها.</p>
        </div>

        <div class="admin-header-actions">
            <a href="{{ route('admin.leads.index') }}" class="admin-btn admin-btn-ghost">
                <i class="fa-solid fa-user-clock"></i>
                مدیریت سرنخ‌ها
            </a>
        </div>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon sky">
                <i class="fa-solid fa-sparkles"></i>
            </div>
            <div>
                <span>سرنخ‌های جدید</span>
                <strong>{{ number_format($stats['new_leads']) }}</strong>
                <small>هنوز پیگیری نشده‌اند</small>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-icon violet">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div>
                <span>ورودی امروز</span>
                <strong>{{ number_format($stats['today_leads']) }}</strong>
                <small>بر اساس تاریخ جلالی امروز</small>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-icon warning">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div>
                <span>نیازمند پیگیری</span>
                <strong>{{ number_format($stats['needs_follow_up']) }}</strong>
                <small>زمان پیگیری رسیده است</small>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-icon primary">
                <i class="fa-solid fa-infinity"></i>
            </div>
            <div>
                <span>مجموع سرنخ‌های فعال</span>
                <strong>{{ number_format($stats['total_leads']) }}</strong>
                <small>بدون احتساب بایگانی‌شده‌ها</small>
            </div>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-header">
            <h2>آخرین سرنخ‌ها</h2>
            <a href="{{ route('admin.leads.index') }}">مشاهده همه</a>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>مشتری</th>
                        <th>خدمت</th>
                        <th>وضعیت</th>
                        <th>ثبت</th>
                        <th>تماس</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentLeads as $lead)
                        <tr>
                            <td>
                                <div class="admin-table-user">
                                    <span class="admin-avatar small">
                                        {{ mb_substr($lead->name, 0, 1) }}
                                    </span>
                                    <div>
                                        <strong>{{ $lead->name }}</strong>
                                        <small>{{ $lead->email ?: 'بدون ایمیل' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $lead->serviceLabel() }}</td>
                            <td>
                                <span class="admin-badge status-{{ $lead->status }}">
                                    {{ $lead->statusLabel() }}
                                </span>
                            </td>
                            <td>{{ Jalali::dateTime($lead->created_at) }}</td>
                            <td dir="ltr">{{ $lead->phone }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="admin-empty">
                                هنوز سرنخی ثبت نشده است. وقتی فرم تماس پر شود، اینجا ستاره‌باران می‌شود. ✨
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>