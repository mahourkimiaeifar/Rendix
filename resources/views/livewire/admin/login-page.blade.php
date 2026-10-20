<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')]
    class extends Component {
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function submit()
    {
        $credentials = $this->validate([
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'ایمیل را وارد کن.',
            'email.email' => 'ایمیل معتبر نیست.',
            'password.required' => 'رمز عبور را وارد کن.',
        ]);

        $key = 'admin-login:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'برای امنیت بیشتر، چند دقیقه صبر کن و دوباره تلاش کن.',
            ]);
        }

        if (!Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($key, 60 * 10);

            throw ValidationException::withMessages([
                'email' => 'ایمیل یا رمز عبور اشتباه است.',
            ]);
        }

        $user = Auth::user();

        if (!$user || !method_exists($user, 'isAdmin') || !$user->isAdmin()) {
            Auth::logout();
            RateLimiter::hit($key, 60 * 10);

            throw ValidationException::withMessages([
                'email' => 'این حساب دسترسی پنل مدیریت رندیکس را ندارد.',
            ]);
        }

        RateLimiter::clear($key);

        request()->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }
};

?>

<div class="rx-auth-wrap">
    <div class="rx-auth-card">

        {{-- سمت برند --}}
        <div class="rx-auth-brand">
            <div class="rx-auth-brand-top">
                <img src="/images/favicon.png?v=6" alt="رندیکس" class="rx-auth-logo">

                <h1 class="rx-auth-title">
                    کهکشان
                    <span>رندیکس</span>
                </h1>

                <p class="rx-auth-subtitle">
                    پنل مدیریت برند، سرنخ‌ها، محتوا و رشد دیجیتال.
                    اینجا همه‌چیز با دقت، سرعت و هویت رندیکس اداره می‌شود.
                </p>
            </div>

            <div class="rx-auth-pills">
                <span class="rx-auth-pill">
                    <i class="fa-solid fa-user-clock"></i>
                    مدیریت سرنخ‌ها
                </span>

                <span class="rx-auth-pill">
                    <i class="fa-solid fa-briefcase"></i>
                    خدمات و محتوا
                </span>

                <span class="rx-auth-pill">
                    <i class="fa-solid fa-chart-line"></i>
                    رشد دیجیتال
                </span>

                <span class="rx-auth-pill">
                    <i class="fa-solid fa-shield-halved"></i>
                    دسترسی امن
                </span>
            </div>

            <div class="rx-auth-metrics">
                <div class="rx-auth-metric">
                    <strong>۱۰۰٪</strong>
                    <span>RTL و فارسی</span>
                </div>

                <div class="rx-auth-metric">
                    <strong>جلالی</strong>
                    <span>تاریخ‌ها</span>
                </div>

                <div class="rx-auth-metric">
                    <strong>Space</strong>
                    <span>UI رندیکس</span>
                </div>
            </div>
        </div>

        {{-- سمت فرم --}}
        <div class="rx-auth-form-side">
            <div class="rx-auth-form-head">
                <h2>ورود مدیران</h2>
                <p>با حساب مدیریتی خود وارد پنل رندیکس شو.</p>
            </div>

            <form wire:submit="submit" class="rx-auth-form">
                @if ($errors->any())
                    <div class="rx-form-errors" role="alert">
                        <div class="rx-form-errors-icon">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>

                        <div>
                            <strong>برای ادامه، این موارد رو تکمیل کن:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="rx-auth-field">
                    <label for="email" class="rx-auth-label">ایمیل مدیریتی</label>

                    <div class="rx-auth-input-shell">
                        <i class="fa-solid fa-envelope rx-auth-input-icon"></i>

                        <input id="email" type="email" wire:model.live="email" class="rx-auth-input"
                            placeholder="admin@rendix.ir" dir="ltr" autocomplete="username" required>
                    </div>

                    @error('email')
                        <p class="rx-auth-error">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div class="rx-auth-field">
                    <label for="password" class="rx-auth-label">رمز عبور</label>

                    <div class="rx-auth-input-shell rx-password-shell" data-rx-password-shell>
                        <i class="fa-solid fa-lock rx-auth-input-icon"></i>

                        <input id="password" type="password" wire:model.live="password"
                            class="rx-auth-input rx-password-input" placeholder="••••••••"
                            autocomplete="current-password" required>

                        <button type="button" class="rx-password-toggle" data-rx-password-toggle
                            aria-label="نمایش رمز عبور" aria-pressed="false" title="نمایش رمز">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>

                    @error('password')
                        <p class="rx-auth-error">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div class="rx-auth-remember">
                    <label class="rx-auth-checkbox">
                        <input type="checkbox" wire:model.live="remember">
                        <span>مرا به خاطر بسپار</span>
                    </label>

                    <span class="rx-auth-secret-note">
                        <i class="fa-solid fa-link-slash"></i>
                        لینک مخفی
                    </span>
                </div>

                <button type="submit" class="rx-auth-submit">
                    <span wire:loading.remove wire:target="submit">
                        ورود به پنل
                        <i class="fa-solid fa-arrow-left"></i>
                    </span>

                    <span wire:loading wire:target="submit">
                        در حال بررسی...
                    </span>
                </button>

                <div class="rx-auth-divider"></div>

                <div class="rx-auth-foot">
                    <span>Rendix Admin Space</span>
                    <span>v1.0</span>
                </div>
            </form>
        </div>
    </div>
</div>