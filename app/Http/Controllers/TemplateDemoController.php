<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Support\Facades\Storage;

class TemplateDemoController extends Controller
{
    /**
     * اجرای زنده فایل دمو داخل iframe
     */
    public function preview(Template $template)
    {
        if (! $template->is_active) {
            abort(404);
        }

        $demo = null;

        try {
            $demo = $template->files()->where('is_demo', true)->first();
        } catch (\Throwable $e) {
            $demo = null;
        }

        if (! $demo || ! Storage::disk('public')->exists($demo->path)) {
            abort(404, 'دمویی برای این قالب ثبت نشده است.');
        }

        $content = Storage::disk('public')->get($demo->path);

        // مسیرهای نسبی (src/href) را به route فایل‌های قالب وصل می‌کنیم
        // تا CSS/JS/تصاویر داخل دمو به‌درستی لود شوند
        $base = route('templates.asset', ['template' => $template->id, 'path' => '']);

        $content = preg_replace(
            '#(src|href)="(?!https?://|//|/|data:|#)([^"]*)"#i',
            '$1="' . $base . '$2"',
            $content
        );

        return response($content, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'X-Frame-Options' => 'SAMEORIGIN',
        ]);
    }

    /**
     * سرو کردن فایل‌های جانبی دمو (css/js/img)
     */
    public function asset(Template $template, string $path)
    {
        $full = 'templates/' . $template->id . '/' . ltrim($path, '/');

        if (! Storage::disk('public')->exists($full)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($full));
    }
}