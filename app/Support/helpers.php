<?php

use App\Support\Settings;

if (! function_exists('setting')) {
    /**
     * Get a setting value.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Settings::get($key, $default);
    }
}

if (! function_exists('setting_url')) {
    /**
     * Get public URL for an uploaded setting image.
     */
    function setting_url(string $key, ?string $fallback = null): ?string
    {
        $path = Settings::get($key);

        if (is_array($path)) {
            $path = $path[0] ?? null;
        }

        if (is_string($path) && $path !== '') {
            $path = str_replace('\\', '/', $path);
            $path = ltrim($path, '/');

            return asset('storage/' . $path);
        }

        return $fallback;
    }
}