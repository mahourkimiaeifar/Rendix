@props([
    'value',
    'format' => 'datetime', // datetime | date
    'seconds' => false,
])

@php
    $iso = null;
    $fallback = '—';

    if ($value instanceof \DateTimeInterface) {
        $carbon = \Carbon\Carbon::instance($value);
        $iso = $carbon->copy()->setTimezone('UTC')->toIso8601String();

        $fallback = $format === 'date'
            ? \App\Support\Jalali::date($carbon)
            : \App\Support\Jalali::dateTime($carbon);
    } elseif (is_string($value) && trim($value) !== '') {
        try {
            $carbon = \Carbon\Carbon::parse($value);
            $iso = $carbon->copy()->setTimezone('UTC')->toIso8601String();

            $fallback = $format === 'date'
                ? \App\Support\Jalali::date($carbon)
                : \App\Support\Jalali::dateTime($carbon);
        } catch (\Throwable $e) {
            $fallback = $value;
        }
    }
@endphp

<time
    class="rx-js-time"
    @if ($iso) data-rx-time="{{ $iso }}" @endif
    data-rx-format="{{ $format }}"
    @if ($seconds) data-rx-seconds="1" @endif
>{{ $fallback ?: '—' }}</time>