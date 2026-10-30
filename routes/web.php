<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Models\ProjectRequest;
use App\Models\ProjectRequestAttachment;
use Illuminate\Support\Facades\Storage;

Volt::route('/', 'pages.home-page')->name('home');
Volt::route('/about', 'pages.about-page')->name('about');
Volt::route('/contact', 'pages.contact-page')->name('contact');

Volt::route('/projects', 'pages.projects-page')->name('projects');
Volt::route('/projects/{slug}', 'pages.project-page')->name('projects.show');

Volt::route('/services', 'pages.services-page')->name('services');
Volt::route('/services/{slug}', 'pages.service-page')->name('services.show');

Volt::route('/request-project', 'pages.request-project-page')->name('request-project');
Volt::route('/request-project/{code}', 'pages.request-tracking-page')->name('request-project.show');

Volt::route('/blog', 'pages.blog-page')->name('blog');
Volt::route('/blog/{slug}', 'pages.post-page')->name('blog.show');

Volt::route('/category/{slug}', 'pages.category-page')->name('category');
Volt::route('/tag/{slug}', 'pages.tag-page')->name('tag');

Volt::route('/page/{slug}', 'pages.page-page')->name('page');

Volt::route('/register', 'auth.register-page')->name('register');
Volt::route('/login', 'auth.login-page')->name('login');

Volt::route('/forgot-password', 'auth.forgot-password-page')->name('password.request');
Volt::route('/reset-password/{token}', 'auth.reset-password-page')->name('password.reset');

Route::post('/newsletter/subscribe', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'email' => ['required', 'email', 'max:190'],
    ], [
        'email.required' => 'ایمیل را وارد کن.',
        'email.email' => 'ایمیل معتبر نیست.',
    ]);

    $email = strtolower(trim($data['email']));

    $subscriber = \App\Models\Subscriber::firstOrNew(['email' => $email]);
    $subscriber->is_active = true;

    if (!$subscriber->exists) {
        $subscriber->save();
    }

    return back()->with('newsletter_status', 'subscribed');
})->name('newsletter.subscribe');

Route::get('/newsletter/unsubscribe/{token}', function (string $token) {
    $subscriber = \App\Models\Subscriber::where('token', $token)->first();

    if ($subscriber) {
        $subscriber->is_active = false;
        $subscriber->save();
    }

    return redirect()->route('home')->with('newsletter_status', 'unsubscribed');
})->name('newsletter.unsubscribe');

Route::post('/logout', function () {
    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Volt::route('/dashboard', 'customer.dashboard-page')->name('dashboard');
    Volt::route('/dashboard/requests', 'customer.requests-page')->name('customer.requests');
    Volt::route('/dashboard/requests/{code}', 'customer.request-show-page')->name('customer.requests.show');
    Volt::route('/dashboard/profile', 'customer.profile-page')->name('customer.profile');

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('dashboard');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');

    Route::get('/dashboard/requests/{code}/attachments/{attachment}', function (string $code, ProjectRequestAttachment $attachment) {
        $user = auth()->user();

        $request = ProjectRequest::query()
            ->where('tracking_code', trim($code))
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere(function ($q) use ($user) {
                        $q->whereNull('user_id')
                            ->where('email', $user->email);
                    });
            })
            ->first();

        if (!$request || $attachment->project_request_id !== $request->id) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($attachment->path)) {
            abort(404);
        }

        return Storage::disk('public')->download($attachment->path, $attachment->original_name);
    })->name('customer.attachments.download');
});

Volt::route('/register', 'auth.register-page')->name('register');
Volt::route('/login', 'auth.login-page')->name('login');

Route::post('/logout', function () {
    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Volt::route('/dashboard', 'customer.dashboard-page')->name('dashboard');
    Volt::route('/dashboard/requests', 'customer.requests-page')->name('customer.requests');
    Volt::route('/dashboard/requests/{code}', 'customer.request-show-page')->name('customer.requests.show');
    Volt::route('/dashboard/profile', 'customer.profile-page')->name('customer.profile');
});

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

    if (Route::has('services')) {
        $urls->push([
            'loc' => route('services'),
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
        //
    }

    try {
        $services = \App\Models\Service::query()
            ->active()
            ->orderByDesc('updated_at')
            ->get();

        foreach ($services as $service) {
            if (Route::has('services.show')) {
                $urls->push([
                    'loc' => route('services.show', $service->slug),
                    'lastmod' => optional($service->updated_at)->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ]);
            }
        }
    } catch (\Throwable $e) {
        //
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
            Route::get('/diagnostics', function () {
                return response(
                    \App\Support\SiteDiagnostics::render(),
                    200
                )->header('Content-Type', 'text/html; charset=UTF-8');
            })->name('diagnostics');

            Route::get('/project-requests/attachments/{attachment}', function (ProjectRequestAttachment $attachment) {
                if (!Storage::disk('public')->exists($attachment->path)) {
                    abort(404);
                }

                return Storage::disk('public')->download($attachment->path, $attachment->original_name);
            })->name('project-requests.attachments.download');

            Volt::route('/posts', 'admin.posts.index-page')->name('posts.index');
            Volt::route('/posts/form', 'admin.posts.form-page')->name('posts.form');

            Volt::route('/categories', 'admin.categories.index-page')->name('categories');
            Volt::route('/tags', 'admin.tags.index-page')->name('tags');
            Volt::route('/pages', 'admin.pages.index-page')->name('pages');

            Volt::route('/project-requests', 'admin.project-requests.index-page')->name('project-requests.index');
            Volt::route('/project-requests/show', 'admin.project-requests.show-page')->name('project-requests.show');

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