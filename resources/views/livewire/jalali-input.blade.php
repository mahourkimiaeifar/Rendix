@props([
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => '۱۴۰۴/۰۷/۲۵',
    'help' => null,
    'required' => false,
])

<div class="admin-field">
    @if ($label)
        <label for="{{ $name }}" class="admin-label">
            {{ $label }}
            @if ($required)
                <span class="admin-required">*</span>
            @endif
        </label>
    @endif

    <div class="admin-date-row">
        <input
            id="{{ $name }}"
            type="text"
            name="{{ $name }}"
            wire:model.live.debounce.400ms="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            class="admin-input admin-jalali-input"
            autocomplete="off"
            dir="ltr"
            @if ($required) required @endif
        >

        <button
            type="button"
            class="admin-icon-btn"
            wire:click="setJalaliToday('{{ $name }}')"
            title="تاریخ امروز"
        >
            <i class="fa-solid fa-calendar-day"></i>
        </button>

        <button
            type="button"
            class="admin-icon-btn"
            wire:click="clearJalali('{{ $name }}')"
            title="پاک کردن تاریخ"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    @if ($help)
        <p class="admin-help">{{ $help }}</p>
    @endif

    @error($name)
        <p class="admin-error">{{ $message }}</p>
    @enderror
</div>