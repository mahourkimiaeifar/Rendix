<?php

use App\Support\Settings;

if (!function_exists('setting')) {
    /**
     * Get a setting value.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Settings::get($key, $default);
    }
}

if (!function_exists('setting_url')) {
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

    if (!function_exists('render_rich_content')) {
        /**
         * Sanitize and render rich HTML content for blog posts and pages.
         */
        function render_rich_content(string $content): string
        {
            $allowed = '<h2><h3><h4><h5><p><br><strong><b><em><i><u><s><ul><ol><li><a><img><blockquote><code><pre><hr><table><thead><tbody><tr><th><td><span><div><figure><figcaption>';

            $clean = strip_tags($content, $allowed);

            $clean = preg_replace('/\son\w+\s*=\s*(["\']).*?\1/i', '', $clean);
            $clean = preg_replace('/\son\w+\s*=\s*[^\s>]+/i', '', $clean);
            $clean = preg_replace('/javascript\s*:/i', '', $clean);
            $clean = preg_replace('/data\s*:\s*text\/html/i', '', $clean);

            return $clean;
        }
    }
}
