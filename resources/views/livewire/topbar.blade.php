@php
    use App\Support\Jalali;
@endphp

<header class="admin-topbar">
    <div class="admin-topbar-start">
        <button id="admin-sidebar-toggle" type="button" class="admin-icon-btn mobile-only">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="admin-breadcrumb">
            <span>پنل رندیکس</span>
            <i class="fa-solid fa-chevron-left"></i>
            <strong>
                @if (request()->routeIs('admin.dashboard'))
                    داشبورد
                @elseif (request()->routeIs('admin.leads.*'))
                    سرنخ‌ها
                @else
                    مدیریت
                @endif
            </strong>
        </div>
    </div>

    <div class="admin-topbar-end">
        <div class="admin-now">
            <i class="fa-solid fa-star"></i>
            <span>{{ Jalali::nowLabel() }}</span>
        </div>

        <details class="admin-user-menu">
            <summary>
                <span class="admin-avatar">
                    {{ mb_substr(auth()->user()->name ?? 'ر', 0, 1) }}
                </span>
                <span class="admin-user-info">
                    <strong>{{ auth()->user()->name ?? 'ادمین' }}</strong>
                    <small>{{ auth()->user()->email ?? '' }}</small>
                </span>
                <i class="fa-solid fa-chevron-down"></i>
            </summary>

            <div class="admin-user-dropdown">
                <a href="#" class="admin-dropdown-item">
                    <i class="fa-solid fa-user-gear"></i>
                    پروفایل من
                </a>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="admin-dropdown-item danger">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        خروج از پنل
                    </button>
                </form>
            </div>
        </details>
    </div>
</header>