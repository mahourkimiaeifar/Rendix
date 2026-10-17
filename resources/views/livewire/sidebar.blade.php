<aside id="admin-sidebar" class="admin-sidebar">
    <div class="admin-sidebar-brand">
        <img src="/images/favicon.png?v=6" alt="رندیکس">
        <div>
            <strong>رندیکس</strong>
            <span>پنل مدیریت</span>
        </div>
    </div>

    <nav class="admin-nav">
        <div class="admin-nav-group">
            <p class="admin-nav-title">فضای کاری</p>

            <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high"></i>
                <span>داشبورد</span>
            </a>
        </div>

        <div class="admin-nav-group">
            <p class="admin-nav-title">مشتری‌ها</p>

            <a href="{{ route('admin.leads.index') }}" class="admin-nav-item {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-clock"></i>
                <span>سرنخ‌ها</span>
            </a>
        </div>

        <div class="admin-nav-group">
            <p class="admin-nav-title">محتوا</p>

            <a href="#" class="admin-nav-item disabled">
                <i class="fa-solid fa-briefcase"></i>
                <span>خدمات</span>
                <em>به‌زودی</em>
            </a>

            <a href="#" class="admin-nav-item disabled">
                <i class="fa-solid fa-diagram-project"></i>
                <span>نمونه‌کارها</span>
                <em>به‌زودی</em>
            </a>

            <a href="#" class="admin-nav-item disabled">
                <i class="fa-solid fa-quote-right"></i>
                <span>نظرات مشتریان</span>
                <em>به‌زودی</em>
            </a>

            <a href="#" class="admin-nav-item disabled">
                <i class="fa-solid fa-circle-question"></i>
                <span>سوالات متداول</span>
                <em>به‌زودی</em>
            </a>
        </div>

        <div class="admin-nav-group">
            <p class="admin-nav-title">سیستم</p>

            <a href="#" class="admin-nav-item disabled">
                <i class="fa-solid fa-sliders"></i>
                <span>تنظیمات</span>
                <em>به‌زودی</em>
            </a>

            <a href="#" class="admin-nav-item disabled">
                <i class="fa-solid fa-photo-film"></i>
                <span>رسانه</span>
                <em>به‌زودی</em>
            </a>

            <a href="#" class="admin-nav-item disabled">
                <i class="fa-solid fa-list-check"></i>
                <span>فعالیت‌ها</span>
                <em>به‌زودی</em>
            </a>
        </div>
    </nav>

    <div class="admin-sidebar-footer">
        <div class="admin-mini-card">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
            <div>
                <strong>رندیکس اِسپیس</strong>
                <span>پنل کهکشانی مدیریت رشد</span>
            </div>
        </div>
    </div>
</aside>