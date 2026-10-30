@php
    $user = auth()->user();
    $siteName = setting('site.name', 'رندیکس');
    $logo = setting_url('brand.logo', asset('images/favicon.png?v=6'));

    $openCount = 0;

    try {
        $openCount = \App\Models\ProjectRequest::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function ($q) use ($user) {
                          $q->whereNull('user_id')->where('email', $user->email);
                      });
            })
            ->whereIn('status', ['new', 'reviewing', 'meeting_scheduled', 'proposal_sent', 'waiting_client', 'in_progress'])
            ->count();
    } catch (\Throwable $e) {
        $openCount = 0;
    }
@endphp

<aside class="rx-cust-sidebar">
    <a href="{{ route('dashboard') }}" class="rx-cust-brand2" wire:navigate>
        <span class="logo-tile">
            <img src="{{ $logo }}" alt="{{ $siteName }}">
        </span>
        <span class="brand-text">
            <strong>{{ $siteName }}</strong>
            <small>ناحیه مشتری</small>
        </span>
    </a>

    <p class="rx-cust-nav-title">مدیریت من</p>

    <a href="{{ route('dashboard') }}" class="rx-cust-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" wire:navigate>
        <span class="nav-ico"><i class="fa-solid fa-gauge-high"></i></span>
        <span>داشبورد</span>
    </a>

    <a href="{{ route('customer.requests') }}" class="rx-cust-nav-item {{ request()->routeIs('customer.requests') ? 'active' : '' }}" wire:navigate>
        <span class="nav-ico"><i class="fa-solid fa-clipboard-list"></i></span>
        <span>درخواست‌های من</span>
        @if ($openCount > 0)
            <span class="nav-count">{{ \App\Support\Jalali::toPersianDigits((string) $openCount) }}</span>
        @endif
    </a>

    <a href="{{ route('request-project') }}" class="rx-cust-nav-item {{ request()->routeIs('request-project') ? 'active' : '' }}" wire:navigate>
        <span class="nav-ico"><i class="fa-solid fa-rocket"></i></span>
        <span>درخواست جدید</span>
    </a>

    <a href="{{ route('customer.profile') }}" class="rx-cust-nav-item {{ request()->routeIs('customer.profile') ? 'active' : '' }}" wire:navigate>
        <span class="nav-ico"><i class="fa-solid fa-user-gear"></i></span>
        <span>پروفایل و رمز عبور</span>
    </a>

    <p class="rx-cust-nav-title" style="margin-top:12px;">دسترسی سریع</p>

    <a href="{{ route('home') }}" class="rx-cust-nav-item" wire:navigate>
        <span class="nav-ico"><i class="fa-solid fa-house"></i></span>
        <span>صفحه اصلی</span>
    </a>

    <a href="{{ route('contact') }}" class="rx-cust-nav-item" wire:navigate>
        <span class="nav-ico"><i class="fa-solid fa-paper-plane"></i></span>
        <span>تماس با ما</span>
    </a>

    <div class="rx-cust-usercard">
        <span class="rx-cust-avatar">{{ mb_substr($user->name ?? 'م', 0, 1) }}</span>
        <span class="rx-cust-usermeta">
            <strong>{{ $user->name ?? 'کاربر' }}</strong>
            <small>{{ $user->email ?? '' }}</small>
        </span>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="rx-cust-nav-item danger">
            <span class="nav-ico"><i class="fa-solid fa-right-from-bracket"></i></span>
            <span>خروج از حساب</span>
        </button>
    </form>
</aside>