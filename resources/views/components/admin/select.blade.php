@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'placeholder' => 'انتخاب کنید...',
    'help' => null,
    'required' => false,
])

@php
    $stringValue = $value === null ? '' : (string) $value;

    $hasEmptyOption = array_key_exists('', $options);
    $hasSelectedValue = $stringValue !== '' || $hasEmptyOption;

    $selectedLabel = $options[$stringValue] ?? (
        $stringValue !== ''
            ? $stringValue
            : $placeholder
    );

    $id = 'rx-select-' . preg_replace('/[^a-zA-Z0-9_-]/', '-', $name);
@endphp

<div class="admin-field rx-select-field" data-rx-select>
    @if ($label)
        <label class="admin-label">
            {{ $label }}

            @if ($required)
                <span class="admin-required">*</span>
            @endif
        </label>
    @endif

    <button
        type="button"
        id="{{ $id }}-button"
        class="rx-select-trigger {{ $hasSelectedValue ? '' : 'is-placeholder' }}"
        data-rx-select-trigger
        aria-haspopup="listbox"
        aria-expanded="false"
        aria-controls="rx-select-global-menu"
    >
        <span class="rx-select-value" data-rx-select-label>
            {{ $selectedLabel ?: $placeholder }}
        </span>

        <i class="fa-solid fa-chevron-down"></i>
    </button>

    <input
        type="hidden"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ $stringValue }}"
        wire:model.live="{{ $name }}"
        data-rx-select-input
        data-rx-select-placeholder="{{ $placeholder }}"
        data-rx-select-empty="{{ $hasEmptyOption ? '1' : '0' }}"
    >

    <script type="application/json" data-rx-select-options>
        @json($options, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)
    </script>

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