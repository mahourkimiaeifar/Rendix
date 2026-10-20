@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'variant' => 'form', // form | filter
    'required' => false,
    'help' => null,
])

@php
    $currentValue = $value === null ? '' : (string) $value;

    $items = [];

    if ($placeholder !== null) {
        $items[] = [
            'value' => '',
            'label' => $placeholder,
        ];
    }

    foreach ($options as $optionValue => $optionLabel) {
        $items[] = [
            'value' => (string) $optionValue,
            'label' => (string) $optionLabel,
        ];
    }

    $id = 'rx-choice-' . preg_replace('/[^a-zA-Z0-9_-]/', '-', $name);
@endphp

<div
    class="admin-field rx-choice-field rx-choice-{{ $variant }}"
    data-rx-choice-field
>
    @if ($label)
        <label class="admin-label" for="{{ $id }}">
            {{ $label }}

            @if ($required)
                <span class="admin-required">*</span>
            @endif
        </label>
    @endif

    @if ($variant === 'filter')
        <div class="rx-choice-control">
            <button
                type="button"
                class="rx-choice-arrow"
                data-rx-choice-prev
                aria-label="گزینه‌های قبلی"
                title="گزینه‌های قبلی"
            >
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <div
                id="{{ $id }}"
                class="rx-choice-track"
                data-rx-choice-track
                role="radiogroup"
                aria-label="{{ $label ?? $name }}"
            >
                @foreach ($items as $item)
                    @php
                        $isChecked = $item['value'] === $currentValue;
                    @endphp

                    <label class="rx-choice-pill {{ $isChecked ? 'is-active' : '' }}">
                        <input
                            type="radio"
                            name="{{ $name }}"
                            value="{{ $item['value'] }}"
                            wire:model.live="{{ $name }}"
                            @checked($isChecked)
                            @if ($required) required @endif
                        >

                        <span title="{{ $item['label'] }}">
                            {{ $item['label'] }}
                        </span>
                    </label>
                @endforeach
            </div>

            <button
                type="button"
                class="rx-choice-arrow"
                data-rx-choice-next
                aria-label="گزینه‌های بعدی"
                title="گزینه‌های بعدی"
            >
                <i class="fa-solid fa-chevron-left"></i>
            </button>
        </div>
    @else
        <div
            id="{{ $id }}"
            class="rx-choice-track rx-choice-wrap"
            data-rx-choice-track
            role="radiogroup"
            aria-label="{{ $label ?? $name }}"
        >
            @foreach ($items as $item)
                @php
                    $isChecked = $item['value'] === $currentValue;
                @endphp

                <label class="rx-choice-pill {{ $isChecked ? 'is-active' : '' }}">
                    <input
                        type="radio"
                        name="{{ $name }}"
                        value="{{ $item['value'] }}"
                        wire:model.live="{{ $name }}"
                        @checked($isChecked)
                        @if ($required) required @endif
                    >

                    <span title="{{ $item['label'] }}">
                        {{ $item['label'] }}
                    </span>
                </label>
            @endforeach
        </div>
    @endif

    @if ($help)
        <p class="admin-help">{{ $help }}</p>
    @endif

    @error($name)
        <p class="admin-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ $message }}</span>
        </p>
    @enderror
</div>