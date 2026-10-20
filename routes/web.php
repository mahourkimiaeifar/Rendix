<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'pages.home-page')->name('home');
Volt::route('/about', 'pages.about-page')->name('about');
Volt::route('/contact', 'pages.contact-page')->name('contact');

Volt::route('/projects', 'pages.projects-page')->name('projects');
Volt::route('/projects/{slug}', 'pages.project-page')->name('projects.show');

Route::get('/sitemap.xml', function () {
    $urls = collect();

    if (Route::has('home')) {
        $urls->push([
            'loc' => route('home'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '1.0',
        ]);
    }

    if (Route::has('about')) {
        $urls->push([
            'loc' => route('about'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.7',
        ]);
    }

    if (Route::has('contact')) {
        $urls->push([
            'loc' => route('contact'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.8',
        ]);
    }

    if (Route::has('projects')) {
        $urls->push([
            'loc' => route('projects'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.9',
        ]);
    }

    try {
        $projects = \App\Models\Project::query()
            ->active()
            ->orderByDesc('updated_at')
            ->get();

        foreach ($projects as $project) {
            if (Route::has('projects.show')) {
                $urls->push([
                    'loc' => route('projects.show', $project->slug),
                    'lastmod' => optional($project->updated_at)->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ]);
            }
        }
    } catch (\Throwable $e) {
        // اگر جدول projects هنوز نبود، sitemap خراب نشود
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($urls as $url) {
        $xml .= "  <url>\n";
        $xml .= '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
        $xml .= '    <lastmod>' . $url['lastmod'] . "</lastmod>\n";
        $xml .= '    <changefreq>' . $url['changefreq'] . "</changefreq>\n";
        $xml .= '    <priority>' . $url['priority'] . "</priority>\n";
        $xml .= "  </url>\n";
    }

    $xml .= '</urlset>';

    return response($xml, 200)
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/robots.txt', function () {
    $prefix = trim((string) config('admin.prefix', 'admin'), '/');

    $body = "User-agent: *\n";
    $body .= "Disallow: /{$prefix}\n";
    $body .= "Disallow: /{$prefix}/*\n";
    $body .= "Allow: /\n";
    $body .= "Sitemap: " . route('sitemap') . "\n";

    return response($body, 200)
        ->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/storage/{path}', function (string $path) {
    $path = str_replace(['..', '\\'], ['', '/'], $path);
    $path = trim($path, '/');

    if ($path === '') {
        abort(404);
    }

    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        abort(404);
    }

    $absolute = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
    $real = realpath($absolute);
    $base = realpath(\Illuminate\Support\Facades\Storage::disk('public')->path(''));

    if (!$real || !$base || !str_starts_with($real, $base)) {
        abort(404);
    }

    return response()->file($real);
})->where('path', '.*');

Route::prefix(config('admin.prefix', 'admin'))
    ->name('admin.')
    ->group(function () {
        Volt::route('/login', 'admin.login-page')->name('login');

        Route::middleware(['admin.auth'])->group(function () {
            Volt::route('/', 'admin.dashboard-page')->name('dashboard');

            Volt::route('/leads', 'admin.leads.index-page')->name('leads.index');
            Volt::route('/leads/show', 'admin.leads.show-page')->name('leads.show');
            Volt::route('/leads/edit', 'admin.leads.edit-page')->name('leads.edit');

            Volt::route('/settings', 'admin.settings-page')->name('settings');

            Volt::route('/services', 'admin.services.index-page')->name('services.index');
            Volt::route('/services/form', 'admin.services.form-page')->name('services.form');

            Volt::route('/projects', 'admin.projects.index-page')->name('projects.index');
            Volt::route('/projects/form', 'admin.projects.form-page')->name('projects.form');

            Volt::route('/testimonials', 'admin.testimonials.index-page')->name('testimonials');
            Volt::route('/faqs', 'admin.faqs.index-page')->name('faqs');

            Volt::route('/profile', 'admin.profile-page')->name('profile');

            Route::post('/logout', function () {
                Auth::logout();

                request()->session()->invalidate();
                request()->session()->regenerateToken();

                return redirect()->route('admin.login');
            })->name('logout');
        });
    });