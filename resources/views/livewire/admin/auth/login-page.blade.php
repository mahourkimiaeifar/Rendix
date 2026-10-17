<?php

use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new class extends Component {
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function submit(): void
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

        if (! Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($key, 60 * 10);

            throw ValidationException::withMessages([
                'email' => 'ایمیل یا رمز عبور اشتباه است.',
            ]);
        }

        $user = Auth::user();

        if (! $user || ! $user->isAdmin()) {
            Auth::logout();
            RateLimiter::hit($key, 60 * 10);

            throw ValidationException::withMessages([
                'email' => 'این حساب دسترسی پنل مدیریت رندیکس را ندارد.',
            ]);
        }

        RateLimiter::clear($key);

        request()->session()->regenerate();

        ActivityLogger::log('admin.login');

        redirect()->intended(route('admin.dashboard'));
    }
};

?>

<div class="admin-auth-card">
    <div class="admin-auth-header">
        <img src="/images/favicon.png?v=6" alt="رندیکس">
        <h1>ورود به کهکشان رندیکس</h1>
        <p>پنل مدیریت برند، سرنخ‌ها، محتوا و رشد دیجیتال.</p>
    </div>

    <form wire:submit="submit" class="admin-auth-form">
        <div class="admin-field">
            <label for="email" class="admin-label">ایمیل مدیریتی</label>
            <input
                id="email"
                type="email"
                wire:model.live="email"
                class="admin-input"
                placeholder="admin@rendix.ir"
                dir="ltr"
                autocomplete="username"
                required
            >
            @error('email') <p class="admin-error">{{ $message }}</p> @enderror
        </div>

        <div class="admin-field">
            <label for="password" class="admin-label">رمز عبور</label>
            <input
                id="password"
                type="password"
                wire:model.live="password"
                class="admin-input"
                placeholder="••••••••"
                autocomplete="current-password"
                required
            >
            @error('password') <p class="admin-error">{{ $message }}</p> @enderror
        </div>

        <label class="admin-checkbox">
            <input type="checkbox" wire:model.live="remember">
            <span>مرا به خاطر بسپار</span>
        </label>

        <button type="submit" class="admin-btn admin-btn-primary w-full">
            <span wire:loading.remove wire:target="submit">
                ورود به پنل
                <i class="fa-solid fa-arrow-left"></i>
            </span>
            <span wire:loading wire:target="submit">
                در حال بررسی...
            </span>
        </button>
    </form>

    <div class="admin-auth-footer">
        <span>Rendix Admin Space</span>
        <span>v1.0</span>
    </div>
</div>