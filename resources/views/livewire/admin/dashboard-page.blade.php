<?php

use App\Models\Lead;
use App\Models\Post;
use App\Models\Project;
use App\Models\ProjectRequest;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {
    public function counts(): array
    {
        $out = ['leads_new' => 0, 'requests_open' => 0, 'posts_live' => 0, 'projects_live' => 0];

        try { $out['leads_new'] = Lead::query()->where('status', 'new')->where('is_archived', false)->count(); } catch (\Throwable $e) {}
        try { $out['requests_open'] = ProjectRequest::query()->whereIn('status', ['new', 'reviewing', 'meeting_scheduled', 'proposal_sent', 'waiting_client', 'in_progress'])->count(); } catch (\Throwable $e) {}
        try { $out['posts_live'] = Post::query()->where('status', 'published')->count(); } catch (\Throwable $e) {}
        try { $out['projects_live'] = Project::query()->where('is_active', true)->count(); } catch (\Throwable $e) {}

        return $out;
    }

    public function recentLeads()
    {
        try { return Lead::query()->orderByDesc('created_at')->limit(5)->get(); } catch (\Throwable $e) { return collect(); }
    }

    public function recentRequests()
    {
        try { return ProjectRequest::query()->orderByDesc('created_at')->limit(5)->get(); } catch (\Throwable $e) { return collect(); }
    }
};

?>

@php
    $user = auth()->user();
    $counts = $this->counts();
    $leads = $this->recentLeads();
    $requests = $this->recentRequests();

    $today = '';

    try {
        $today = Jalali::date(now());
    } catch (\Throwable $e) {
        $today = now()->format('Y/m/d');
    }

    $hour = (int) now()->format('G');
    $greet = $hour < 12 ? 'صبح بخیر' : ($hour < 17 ? 'ظهر بخیر' : 'عصر بخیر');

    $leadBadge = function (string $status): string {
        return match ($status) {
            'new' => 'status-new',
            'contacted' => 'status-contacted',
            'negotiation' => 'status-negotiation',
            'proposal' => 'status-proposal',
            'won' => 'status-won',
            'lost' => 'status-lost',
            default => 'gray',
        };
    };

    $requestBadge = function (string $status): string {
        return match ($status) {
            'delivered' => 'success',
            'cancelled', 'archived' => 'danger',
            'waiting_client', 'proposal_sent' => 'primary',
            'new' => 'status-new',
            default => 'warning',
        };
    };
@endphp

@once
<style>
.rx-dash-duo{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(0,.85fr);gap:18px;align-items:start}
.rx-dash-quick{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;padding:14px}
.rx-dash-q{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:14px;background:rgba(255,255,255,.035);border:1px solid rgba(255,255,255,.08);color:var(--rx-muted);font-size:13px;font-weight:850;text-decoration:none;transition:all .22s ease}
.rx-dash-q i{width:34px;height:34px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;background:rgba(69,200,255,.10);border:1px solid rgba(69,200,255,.18);color:var(--rx-sky);transition:all .22s ease}
.rx-dash-q:hover{color:var(--rx-snow);border-color:rgba(69,200,255,.26);background:rgba(69,200,255,.06);transform:translateY(-2px)}
.rx-dash-q:hover i{color:#fff;background:linear-gradient(135deg,var(--rx-primary),var(--rx-sky));border-color:transparent}
.rx-dash-q.gold i{background:rgba(245,184,65,.10);border-color:rgba(245,184,65,.20);color:#F5B841}
.rx-dash-q.mint i{background:rgba(45,212,167,.10);border-color:rgba(45,212,167,.20);color:#2DD4A7}
.rx-dash-q.violet i{background:rgba(118,83,246,.12);border-color:rgba(118,83,246,.22);color:var(--rx-violet)}
.rx-dash-q.coral i{background:rgba(255,107,74,.10);border-color:rgba(255,107,74,.20);color:#FF6B4A}
.admin-stat-icon.gold{color:#F5B841;background:rgba(245,184,65,.10);border-color:rgba(245,184,65,.22)}
.admin-stat-icon.mint{color:#2DD4A7;background:rgba(45,212,167,.10);border-color:rgba(45,212,167,.22)}
@media(max-width:1100px){.rx-dash-duo{grid-template-columns:1fr}}
@media(max-width:720px){.rx-dash-quick{grid-template-columns:1fr}}
</style>
@endonce

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>{{ $greet }}، {{ $user->name ?? 'مدیر' }} 👋</h1>
            <p>نمای کلی وضعیت رندیکس در یک نگاه — {{ $today }}</p>
        </div>

        <div class="admin-header-actions">
            @if (Route::has('admin.diagnostics'))
                <a href="{{ route('admin.diagnostics') }}" class="admin-btn admin-btn-ghost">
                    <i class="fa-solid fa-stethoscope"></i>
                    تشخیص سایت
                </a>
            @endif

            @if (Route::has('admin.project-requests.index'))
                <a href="{{ route('admin.project-requests.index') }}" class="admin-btn admin-btn-primary">
                    <i class="fa-solid fa-clipboard-list"></i>
                    درخواست‌های پروژه
                </a>
            @endif
        </div>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon sky"><i class="fa-solid fa-user-plus"></i></div>
            <div>
                <span>سرنخ‌های جدید</span>
                <strong>{{ Jalali::toPersianDigits((string) $counts['leads_new']) }}</strong>
                <small>منتظر اولین تماس</small>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-icon gold"><i class="fa-solid fa-hourglass-half"></i></div>
            <div>
                <span>درخواست‌های باز</span>
                <strong>{{ Jalali::toPersianDigits((string) $counts['requests_open']) }}</strong>
                <small>پروژه‌های در جریان بررسی</small>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-icon violet"><i class="fa-solid fa-newspaper"></i></div>
            <div>
                <span>مقالات منتشرشده</span>
                <strong>{{ Jalali::toPersianDigits((string) $counts['posts_live']) }}</strong>
                <small>محتوای سئوی فعال</small>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-icon mint"><i class="fa-solid fa-diagram-project"></i></div>
            <div>
                <span>نمونه‌کارهای فعال</span>
                <strong>{{ Jalali::toPersianDigits((string) $counts['projects_live']) }}</strong>
                <small>ویترین عمومی رندیکس</small>
            </div>
        </div>
    </div>

    <div class="rx-dash-duo">
        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>آخرین سرنخ‌ها</h2>
                @if (Route::has('admin.leads.index'))
                    <a href="{{ route('admin.leads.index') }}">مشاهده همه</a>
                @endif
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>مشتری</th>
                            <th>خدمت</th>
                            <th>وضعیت</th>
                            <th>ثبت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leads as $lead)
                            <tr>
                                <td>
                                    <div class="admin-table-user">
                                        <span class="admin-avatar small">{{ mb_substr($lead->name, 0, 1) }}</span>
                                        <div>
                                            <strong>{{ $lead->name }}</strong>
                                            <small dir="ltr">{{ $lead->phone }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $lead->service }}</td>
                                <td><span class="admin-badge {{ $leadBadge($lead->status) }}">{{ $lead->statusLabel() }}</span></td>
                                <td>{{ Jalali::date($lead->created_at) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="admin-empty">هنوز سرنخی ثبت نشده است.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>آخرین درخواست‌های پروژه</h2>
                @if (Route::has('admin.project-requests.index'))
                    <a href="{{ route('admin.project-requests.index') }}">همه</a>
                @endif
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table" style="min-width:480px;">
                    <thead>
                        <tr>
                            <th>کد</th>
                            <th>وضعیت</th>
                            <th>ثبت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $request)
                            <tr>
                                <td>
                                    <strong dir="ltr" style="color:var(--rx-sky);font-size:12px;">{{ $request->tracking_code }}</strong>
                                    <small style="display:block;color:var(--rx-soft);font-size:11px;margin-top:3px;">{{ $request->name }}</small>
                                </td>
                                <td><span class="admin-badge {{ $requestBadge($request->status) }}">{{ $request->statusLabel() }}</span></td>
                                <td>{{ Jalali::date($request->created_at) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="admin-empty">هنوز درخواست پروژه‌ای ثبت نشده است.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-header">
            <h2>دسترسی سریع</h2>
        </div>

        <div class="rx-dash-quick">
            @if (Route::has('admin.posts.form'))
                <a href="{{ route('admin.posts.form') }}" class="rx-dash-q violet"><i class="fa-solid fa-pen-nib"></i><span>نوشتن مقاله جدید</span></a>
            @endif
            @if (Route::has('admin.services.form'))
                <a href="{{ route('admin.services.form') }}" class="rx-dash-q"><i class="fa-solid fa-layer-group"></i><span>افزودن خدمت</span></a>
            @endif
            @if (Route::has('admin.projects.form'))
                <a href="{{ route('admin.projects.form') }}" class="rx-dash-q mint"><i class="fa-solid fa-images"></i><span>ثبت نمونه‌کار</span></a>
            @endif
            @if (Route::has('admin.settings'))
                <a href="{{ route('admin.settings') }}" class="rx-dash-q gold"><i class="fa-solid fa-sliders"></i><span>تنظیمات سایت</span></a>
            @endif
            @if (Route::has('admin.testimonials'))
                <a href="{{ route('admin.testimonials') }}" class="rx-dash-q coral"><i class="fa-solid fa-quote-right"></i><span>نظرات مشتریان</span></a>
            @endif
            @if (Route::has('admin.faqs'))
                <a href="{{ route('admin.faqs') }}" class="rx-dash-q"><i class="fa-solid fa-circle-question"></i><span>سوالات متداول</span></a>
            @endif
        </div>
    </div>
</div>