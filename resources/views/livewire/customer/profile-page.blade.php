<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.customer')]
    class extends Component {
    public string $name = '';
    public string $email = '';
    public string $phone = '';

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public bool $saved = false;

    public function mount(): void
    {
        $user = auth()->user();

        $this->name = (string) ($user->name ?? '');
        $this->email = (string) ($user->email ?? '');
        $this->phone = (string) ($user->phone ?? '');
    }

    public function save()
    {
        $user = auth()->user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'min:10', 'max:20'],
            'current_password' => ['required_with:password', 'string'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ], [
            'name.required' => 'نام را وارد کن.',
            'email.required' => 'ایمیل را وارد کن.',
            'email.email' => 'ایمیل معتبر نیست.',
            'email.unique' => 'این ایمیل برای کاربر دیگری ثبت شده است.',
            'current_password.required_with' => 'برای تغییر رمز، رمز فعلی لازم است.',
            'password.confirmed' => 'تکرار رمز مطابق نیست.',
            'password.min' => 'رمز جدید باید حداقل ۸ کاراکتر باشد.',
        ]);

        if (!empty($validated['password']) && !Hash::check($validated['current_password'], $user->password)) {
            $this->addError('current_password', 'رمز عبور فعلی درست نیست.');

            return;
        }

        $user->name = trim($validated['name']);
        $user->email = trim($validated['email']);
        $user->forceFill(['phone' => trim($validated['phone'] ?? '') ?: null]);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $this->reset(['current_password', 'password', 'password_confirmation']);

        $this->saved = true;
    }
};

?>

@section('cust.title', 'پروفایل و رمز عبور')
@section('cust.icon', 'fa-solid fa-user-gear')

@if ($saved)
    <div class="st-pill st-done" style="padding:12px 16px;font-size:13px;">
        <i class="fa-solid fa-circle-check"></i>
        تغییرات با موفقیت ذخیره شد.
    </div>
@endif
<div>
    <div class="rx-cust-card" style="padding:20px;">
        <h2 style="margin:0 0 16px;font-size:16px;font-weight:950;">اطلاعات حساب</h2>

        <form wire:submit="save" style="display:grid;gap:14px;">
            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;" class="rx-profile-grid2">
                <div class="rx-field">
                    <label class="rx-cust-muted"
                        style="display:block;margin-bottom:6px;font-size:12px;font-weight:800;">نام و نام خانوادگی
                        *</label>
                    <input type="text" wire:model="name" class="rx-select"
                        style="background-image:none;padding-left:14px;" required>
                    @error('name') <span class="text-coral"
                    style="font-size:12px;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                </div>

                <div class="rx-field">
                    <label class="rx-cust-muted"
                        style="display:block;margin-bottom:6px;font-size:12px;font-weight:800;">ایمیل *</label>
                    <input type="email" wire:model="email" dir="ltr" class="rx-select"
                        style="background-image:none;padding-left:14px;" required>
                    @error('email') <span class="text-coral"
                    style="font-size:12px;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                </div>

                <div class="rx-field" style="grid-column:1/-1;">
                    <label class="rx-cust-muted"
                        style="display:block;margin-bottom:6px;font-size:12px;font-weight:800;">شماره تماس</label>
                    <input type="tel" wire:model="phone" dir="ltr" class="rx-select"
                        style="background-image:none;padding-left:14px;">
                    @error('phone') <span class="text-coral"
                    style="font-size:12px;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="border-top:1px solid var(--rx-border);padding-top:16px;display:grid;gap:14px;">
                <h3 style="margin:0;font-size:14px;font-weight:950;">تغییر رمز عبور <span class="rx-cust-muted"
                        style="font-weight:700;">(اختیاری)</span></h3>

                <div class="rx-field">
                    <label class="rx-cust-muted"
                        style="display:block;margin-bottom:6px;font-size:12px;font-weight:800;">رمز فعلی</label>
                    <input type="password" wire:model="current_password" class="rx-select"
                        style="background-image:none;padding-left:14px;">
                    @error('current_password') <span class="text-coral"
                    style="font-size:12px;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                </div>

                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;"
                    class="rx-profile-grid2">
                    <div class="rx-field">
                        <label class="rx-cust-muted"
                            style="display:block;margin-bottom:6px;font-size:12px;font-weight:800;">رمز جدید</label>
                        <input type="password" wire:model="password" class="rx-select"
                            style="background-image:none;padding-left:14px;">
                        @error('password') <span class="text-coral"
                        style="font-size:12px;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                    </div>

                    <div class="rx-field">
                        <label class="rx-cust-muted"
                            style="display:block;margin-bottom:6px;font-size:12px;font-weight:800;">تکرار رمز
                            جدید</label>
                        <input type="password" wire:model="password_confirmation" class="rx-select"
                            style="background-image:none;padding-left:14px;">
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        <i class="fa-solid fa-floppy-disk"></i>
                        ذخیره تغییرات
                    </span>
                    <span wire:loading wire:target="save">در حال ذخیره...</span>
                </button>
            </div>
        </form>
    </div>

    <style>
        @media (max-width: 760px) {
            .rx-profile-grid2 {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</div>