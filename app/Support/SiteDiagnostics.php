<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SiteDiagnostics
{
    protected static array $sections = [];

    protected static array $summary = [
        'pass' => 0,
        'warn' => 0,
        'fail' => 0,
        'info' => 0,
    ];

    public static function render(): string
    {
        self::$sections = [];
        self::$summary = [
            'pass' => 0,
            'warn' => 0,
            'fail' => 0,
            'info' => 0,
        ];

        self::run();

        return self::html();
    }

    protected static function add(
        string $section,
        string $label,
        string $status,
        string $value = '',
        string $hint = ''
    ): void {
        if (! in_array($status, ['pass', 'warn', 'fail', 'info'], true)) {
            $status = 'info';
        }

        if (! isset(self::$sections[$section])) {
            self::$sections[$section] = [];
        }

        self::$sections[$section][] = [
            'label' => $label,
            'status' => $status,
            'value' => $value,
            'hint' => $hint,
        ];

        self::$summary[$status] = (self::$summary[$status] ?? 0) + 1;
    }

    protected static function run(): void
    {
        self::checkEnvironment();
        self::checkSecurity();
        self::checkDatabase();
        self::checkClasses();
        self::checkViews();
        self::checkRoutes();
        self::checkContactPageLayout();
        self::checkHelpers();
        self::checkStorage();
        self::checkAssets();
        self::checkSettingsValues();
        self::checkCache();
        self::checkMaintenance();
        self::checkLogs();
        self::checkCurrentUser();
    }

    protected static function checkEnvironment(): void
    {
        self::add('عمومی', 'نسخه Laravel', 'info', app()->version());

        $phpVersion = PHP_VERSION;
        self::add(
            'عمومی',
            'نسخه PHP',
            version_compare($phpVersion, '8.1.0', '>=') ? 'pass' : 'fail',
            $phpVersion,
            'حداقل PHP مورد نیاز: 8.1'
        );

        $extensions = [
            'mbstring',
            'openssl',
            'pdo',
            'tokenizer',
            'xml',
            'ctype',
            'json',
            'curl',
            'fileinfo',
        ];

        foreach ($extensions as $extension) {
            self::add(
                'عمومی',
                'اکستنشن ' . $extension,
                extension_loaded($extension) ? 'pass' : 'fail',
                extension_loaded($extension) ? 'فعال' : 'غیرفعال'
            );
        }

        self::add('عمومی', 'Environment', 'info', app()->environment());
        self::add('عمومی', 'Timezone', 'info', (string) config('app.timezone'));
        self::add('عمومی', 'Locale', 'info', (string) config('app.locale'));
        self::add('عمومی', 'Fallback Locale', 'info', (string) config('app.fallback_locale'));

        $appUrl = (string) config('app.url');
        self::add(
            'عمومی',
            'APP_URL',
            $appUrl !== '' ? 'pass' : 'warn',
            $appUrl !== '' ? $appUrl : 'تنظیم نشده',
            'برای سئو، لینک‌های مطلق و OG، APP_URL باید تنظیم باشد.'
        );
    }

    protected static function checkSecurity(): void
    {
        $appKey = (string) config('app.key');

        self::add(
            'امنیت',
            'APP_KEY',
            $appKey !== '' ? 'pass' : 'fail',
            $appKey !== '' ? 'ست شده' : 'خالی است',
            'اگر خالی باشد، Session/Encrypter/ Livewire ممکن است مشکل بگیرد.'
        );

        $debug = (bool) config('app.debug');
        $isProduction = app()->environment('production');

        self::add(
            'امنیت',
            'APP_DEBUG',
            $debug && $isProduction ? 'fail' : ($debug ? 'warn' : 'pass'),
            $debug ? 'روشن' : 'خاموش',
            $isProduction
                ? 'در محیط production حتماً باید خاموش باشد.'
                : 'در محیط local روشن بودن اشکالی ندارد.'
        );

        $prefix = trim((string) config('admin.prefix', 'admin'), '/');

        self::add(
            'امنیت',
            'پیشوند پنل مدیریت',
            $prefix !== '' && $prefix !== 'admin' ? 'pass' : 'warn',
            $prefix !== '' ? $prefix : 'تنظیم نشده',
            'برای تولید، پیشوند غیر از admin توصیه می‌شود.'
        );

        self::add(
            'امنیت',
            'Session Driver',
            'info',
            (string) config('session.driver')
        );

        self::add(
            'امنیت',
            'Cache Driver',
            'info',
            (string) config('cache.default')
        );

        self::add(
            'امنیت',
            'Filesystem Disk',
            'info',
            (string) config('filesystems.default')
        );
    }

    protected static function checkDatabase(): void
    {
        try {
            DB::connection()->getPdo();

            self::add(
                'دیتابیس',
                'اتصال به دیتابیس',
                'pass',
                (string) config('database.default')
            );
        } catch (Throwable $e) {
            self::add(
                'دیتابیس',
                'اتصال به دیتابیس',
                'fail',
                'خطا',
                $e->getMessage()
            );

            return;
        }

        $tables = [
            'users' => 'fail',
            'leads' => 'fail',
            'lead_notes' => 'warn',
            'activity_logs' => 'warn',
            'settings' => 'fail',
            'services' => 'fail',
            'projects' => 'fail',
            'testimonials' => 'fail',
            'faqs' => 'fail',
        ];

        foreach ($tables as $table => $requiredStatus) {
            try {
                $exists = Schema::hasTable($table);

                self::add(
                    'دیتابیس',
                    'جدول ' . $table,
                    $exists ? 'pass' : $requiredStatus,
                    $exists ? 'موجود' : 'موجود نیست'
                );

                if ($exists) {
                    try {
                        $count = DB::table($table)->count();

                        self::add(
                            'دیتابیس',
                            'تعداد رکوردهای ' . $table,
                            'info',
                            number_format($count)
                        );
                    } catch (Throwable $e) {
                        self::add(
                            'دیتابیس',
                            'شمارش ' . $table,
                            'warn',
                            'ناموفق',
                            $e->getMessage()
                        );
                    }
                }
            } catch (Throwable $e) {
                self::add(
                    'دیتابیس',
                    'بررسی جدول ' . $table,
                    'fail',
                    'خطا',
                    $e->getMessage()
                );
            }
        }

        $columns = [
            'users' => [
                'email' => 'fail',
                'password' => 'fail',
                'name' => 'warn',
                'role' => 'warn',
            ],
            'leads' => [
                'name' => 'fail',
                'email' => 'fail',
                'phone' => 'fail',
                'service' => 'fail',
                'message' => 'fail',
                'status' => 'fail',
                'priority' => 'fail',
                'metadata' => 'fail',
                'is_archived' => 'fail',
                'next_follow_up_at' => 'fail',
                'last_contact_at' => 'fail',
                'source_page' => 'warn',
            ],
            'lead_notes' => [
                'lead_id' => 'warn',
                'user_id' => 'warn',
                'type' => 'warn',
                'body' => 'warn',
            ],
            'settings' => [
                'key' => 'fail',
                'value' => 'fail',
            ],
            'services' => [
                'title' => 'fail',
                'slug' => 'fail',
                'is_active' => 'fail',
                'sort_order' => 'fail',
                'short_description' => 'warn',
                'description' => 'warn',
                'image_path' => 'warn',
            ],
            'projects' => [
                'title' => 'fail',
                'slug' => 'fail',
                'category' => 'fail',
                'is_active' => 'fail',
                'is_featured' => 'fail',
                'sort_order' => 'fail',
                'short_description' => 'warn',
                'challenge' => 'warn',
                'solution' => 'warn',
                'result' => 'warn',
                'image_path' => 'warn',
                'gallery' => 'warn',
            ],
            'testimonials' => [
                'name' => 'fail',
                'content' => 'fail',
                'rating' => 'fail',
                'is_active' => 'fail',
                'sort_order' => 'fail',
                'avatar_path' => 'warn',
            ],
            'faqs' => [
                'question' => 'fail',
                'answer' => 'fail',
                'location' => 'fail',
                'is_active' => 'fail',
                'sort_order' => 'fail',
            ],
        ];

        foreach ($columns as $table => $tableColumns) {
            try {
                if (! Schema::hasTable($table)) {
                    continue;
                }

                foreach ($tableColumns as $column => $requiredStatus) {
                    $exists = Schema::hasColumn($table, $column);

                    self::add(
                        'دیتابیس',
                        $table . '.' . $column,
                        $exists ? 'pass' : $requiredStatus,
                        $exists ? 'موجود' : 'موجود نیست'
                    );
                }
            } catch (Throwable $e) {
                self::add(
                    'دیتابیس',
                    'بررسی ستون‌های ' . $table,
                    'warn',
                    'خطا',
                    $e->getMessage()
                );
            }
        }
    }

    protected static function checkClasses(): void
    {
        $classes = [
            \App\Models\User::class => 'fail',
            \App\Models\Lead::class => 'fail',
            \App\Models\Setting::class => 'fail',
            \App\Models\Service::class => 'fail',
            \App\Models\Project::class => 'fail',
            \App\Models\Testimonial::class => 'fail',
            \App\Models\Faq::class => 'fail',
            \App\Support\Settings::class => 'fail',
            \App\Support\Jalali::class => 'fail',
            \App\Support\MaintenanceMode::class => 'fail',
            \App\Services\ActivityLogger::class => 'warn',
            \App\Models\LeadNote::class => 'warn',
            \App\Http\Middleware\RenderMaintenanceMode::class => 'warn',
        ];

        foreach ($classes as $class => $requiredStatus) {
            self::add(
                'کلاس‌ها',
                class_basename($class),
                class_exists($class) ? 'pass' : $requiredStatus,
                class_exists($class) ? 'موجود' : 'موجود نیست',
                $class
            );
        }
    }

    protected static function checkViews(): void
    {
        $views = [
            'components.layouts.app' => 'fail',
            'components.layouts.admin' => 'fail',
            'components.site.footer' => 'fail',
            'components.site.social-links' => 'fail',
            'components.site.faq' => 'warn',
            'components.site.testimonials' => 'warn',
            'components.site.admin-link' => 'warn',
            'components.admin.choice-pills' => 'warn',
            'components.admin.form-errors' => 'warn',
            'components.admin.jalali-input' => 'warn',
            'components.admin.select' => 'info',
            'livewire.pages.home-page' => 'fail',
            'livewire.pages.about-page' => 'fail',
            'livewire.pages.contact-page' => 'fail',
            'livewire.pages.projects-page' => 'warn',
            'livewire.pages.project-page' => 'warn',
            'livewire.admin.login-page' => 'fail',
            'livewire.admin.dashboard-page' => 'fail',
            'livewire.admin.leads.index-page' => 'fail',
            'livewire.admin.leads.show-page' => 'fail',
            'livewire.admin.leads.edit-page' => 'fail',
            'livewire.admin.settings-page' => 'fail',
            'livewire.admin.services.index-page' => 'fail',
            'livewire.admin.services.form-page' => 'fail',
            'livewire.admin.projects.index-page' => 'fail',
            'livewire.admin.projects.form-page' => 'fail',
            'livewire.admin.testimonials.index-page' => 'fail',
            'livewire.admin.faqs.index-page' => 'fail',
            'livewire.admin.profile-page' => 'fail',
            'maintenance' => 'warn',
        ];

        foreach ($views as $view => $requiredStatus) {
            try {
                $exists = view()->exists($view);

                self::add(
                    'ویوها',
                    $view,
                    $exists ? 'pass' : $requiredStatus,
                    $exists ? 'موجود' : 'موجود نیست'
                );
            } catch (Throwable $e) {
                self::add(
                    'ویوها',
                    $view,
                    'warn',
                    'خطا در بررسی',
                    $e->getMessage()
                );
            }
        }
    }

    protected static function checkRoutes(): void
    {
        $routes = [
            'home' => 'fail',
            'about' => 'fail',
            'contact' => 'fail',
            'projects' => 'warn',
            'projects.show' => 'warn',
            'sitemap' => 'warn',
            'robots' => 'warn',
            'admin.login' => 'fail',
            'admin.dashboard' => 'fail',
            'admin.leads.index' => 'fail',
            'admin.leads.show' => 'fail',
            'admin.leads.edit' => 'fail',
            'admin.settings' => 'fail',
            'admin.services.index' => 'fail',
            'admin.services.form' => 'fail',
            'admin.projects.index' => 'fail',
            'admin.projects.form' => 'fail',
            'admin.testimonials' => 'fail',
            'admin.faqs' => 'fail',
            'admin.profile' => 'fail',
            'admin.logout' => 'fail',
            'admin.diagnostics' => 'info',
        ];

        foreach ($routes as $name => $requiredStatus) {
            $exists = Route::has($name);

            self::add(
                'مسیرها',
                $name,
                $exists ? 'pass' : $requiredStatus,
                $exists ? 'تعریف شده' : 'تعریف نشده'
            );
        }

        $hasStorageFallback = collect(Route::getRoutes())
            ->contains(fn ($route) => $route->uri() === 'storage/{path}');

        self::add(
            'مسیرها',
            'fallback /storage/{path}',
            $hasStorageFallback ? 'pass' : 'warn',
            $hasStorageFallback ? 'فعال' : 'فعال نیست',
            'اگر storage:link روی ویندوز مشکل داشته باشد، این route تصاویر را سرو می‌کند.'
        );
    }

    protected static function checkContactPageLayout(): void
    {
        $path = resource_path('views/livewire/pages/contact-page.blade.php');

        if (! File::exists($path)) {
            self::add(
                'صفحه تماس',
                'فایل contact-page.blade.php',
                'fail',
                'وجود ندارد',
                $path
            );

            return;
        }

        $contents = File::get($path);

        $hasLayout = (bool) preg_match(
            '/Layout\s*\(\s*[\'"](?:components\.)?layouts\.app[\'"]\s*\)/u',
            $contents
        );

        self::add(
            'صفحه تماس',
            'داشتن Layout در صفحه تماس',
            $hasLayout ? 'pass' : 'fail',
            $hasLayout ? 'دارد' : 'ندارد',
            $hasLayout
                ? 'صفحه تماس باید از لایوت اصلی سایت استفاده کند تا CSS/JS/React background لود شود.'
                : 'اگر این خط را نداشته باشد، صفحه تماس استایل نمی‌گیرد. باید دارای #[Layout(\'components.layouts.app\')] باشد.'
        );

        $hasServiceOptions = str_contains($contents, 'serviceOptions');

        self::add(
            'صفحه تماس',
            'اتصال به خدمات داینامیک',
            $hasServiceOptions ? 'pass' : 'warn',
            $hasServiceOptions ? 'دارد' : 'ندارد',
            'برای اینکه گزینه‌های خدمت از دیتابیس بیایند.'
        );

        $hasSetting = str_contains($contents, 'setting(');

        self::add(
            'صفحه تماس',
            'اتصال به تنظیمات سایت',
            $hasSetting ? 'pass' : 'warn',
            $hasSetting ? 'دارد' : 'ندارد',
            'برای پیام موفقیت، اطلاعات تماس و فعال/غیرفعال بودن فرم.'
        );
    }

    protected static function checkHelpers(): void
    {
        self::add(
            'هلپر‌ها',
            'تابع setting()',
            function_exists('setting') ? 'pass' : 'fail',
            function_exists('setting') ? 'loaded' : 'load نشده',
            'اگر load نشده باشد، composer dump-autoload -o لازم است.'
        );

        self::add(
            'هلپر‌ها',
            'تابع setting_url()',
            function_exists('setting_url') ? 'pass' : 'fail',
            function_exists('setting_url') ? 'loaded' : 'load نشده',
            'برای نمایش درست لوگو/favicon/OG آپلودشده از پنل.'
        );
    }

    protected static function checkStorage(): void
    {
        $publicRoot = config('filesystems.disks.public.root');

        if (! is_string($publicRoot) || $publicRoot === '') {
            self::add(
                'استوریج',
                'مسیر دیسک public',
                'fail',
                'تنظیم نشده',
                'config/filesystems.php -> disks.public.root'
            );
        } else {
            $exists = File::isDirectory($publicRoot);
            $writable = $exists && File::isWritable($publicRoot);

            self::add(
                'استوریج',
                'پوشه storage/app/public',
                $exists ? ($writable ? 'pass' : 'warn') : 'fail',
                $exists ? ($writable ? 'موجود و قابل نوشتن' : 'موجود ولی غیرقابل نوشتن') : 'موجود نیست',
                $publicRoot
            );
        }

        $symlinkExists = File::isDirectory(public_path('storage'));

        $hasStorageFallback = collect(Route::getRoutes())
            ->contains(fn ($route) => $route->uri() === 'storage/{path}');

        if ($symlinkExists) {
            self::add(
                'استوریج',
                'public/storage',
                'pass',
                'موجود',
                'storage:link درست ساخته شده است.'
            );
        } elseif ($hasStorageFallback) {
            self::add(
                'استوریج',
                'public/storage',
                'warn',
                'symlink نیست، ولی fallback route فعال است',
                'اگر php artisan storage:link روی ویندوز خطا داد، route /storage/{path} کار را راه می‌اندازد.'
            );
        } else {
            self::add(
                'استوریج',
                'public/storage',
                'fail',
                'موجود نیست و fallback route هم فعال نیست',
                'یا storage:link را بساز، یا route /storage/{path} را اضافه کن.'
            );
        }

        $maintenanceDir = storage_path('app');
        $maintenanceFile = storage_path('app/maintenance.json');

        self::add(
            'استوریج',
            'پوشه storage/app',
            File::isDirectory($maintenanceDir) && File::isWritable($maintenanceDir)
                ? 'pass'
                : 'fail',
            File::isDirectory($maintenanceDir)
                ? (File::isWritable($maintenanceDir) ? 'قابل نوشتن' : 'غیرقابل نوشتن')
                : 'موجود نیست',
            $maintenanceDir
        );

        self::add(
            'استوریج',
            'فایل maintenance.json',
            File::exists($maintenanceFile) ? 'info' : 'warn',
            File::exists($maintenanceFile) ? 'موجود' : 'هنوز ساخته نشده',
            $maintenanceFile
        );

        $logDir = storage_path('logs');

        self::add(
            'استوریج',
            'پوشه storage/logs',
            File::isDirectory($logDir) && File::isWritable($logDir)
                ? 'pass'
                : 'warn',
            File::isDirectory($logDir)
                ? (File::isWritable($logDir) ? 'قابل نوشتن' : 'غیرقابل نوشتن')
                : 'موجود نیست',
            $logDir
        );

        $frameworkDirs = [
            'storage/framework/views',
            'storage/framework/cache',
            'storage/framework/sessions',
            'bootstrap/cache',
        ];

        foreach ($frameworkDirs as $dir) {
            $path = $dir === 'bootstrap/cache'
                ? base_path('bootstrap/cache')
                : storage_path(str_replace('storage/', '', $dir));

            $exists = File::isDirectory($path);
            $writable = $exists && File::isWritable($path);

            self::add(
                'استوریج',
                $dir,
                $exists ? ($writable ? 'pass' : 'warn') : 'fail',
                $exists ? ($writable ? 'قابل نوشتن' : 'غیرقابل نوشتن') : 'موجود نیست',
                $path
            );
        }

        self::checkUploadedSettingImages();
    }

    protected static function checkUploadedSettingImages(): void
    {
        if (! class_exists(Settings::class)) {
            return;
        }

        $keys = [
            'brand.logo',
            'brand.favicon',
            'brand.og_image',
        ];

        foreach ($keys as $key) {
            try {
                $raw = Settings::get($key);

                if ($raw === null || $raw === '') {
                    self::add(
                        'تصاویر تنظیمات',
                        $key,
                        'info',
                        'تنظیم نشده',
                        'از fallback استفاده می‌شود.'
                    );

                    continue;
                }

                $normalized = self::normalizeUploadedPath($raw);

                if ($normalized['type'] === 'url') {
                    self::add(
                        'تصاویر تنظیمات',
                        $key,
                        'info',
                        'URL خارجی',
                        $normalized['path']
                    );

                    continue;
                }

                $path = $normalized['path'];

                if ($path === '') {
                    self::add(
                        'تصاویر تنظیمات',
                        $key,
                        'warn',
                        'مسیر نامعتبر',
                        is_scalar($raw) ? (string) $raw : json_encode($raw, JSON_UNESCAPED_UNICODE)
                    );

                    continue;
                }

                $exists = Storage::disk('public')->exists($path);

                self::add(
                    'تصاویر تنظیمات',
                    $key,
                    $exists ? 'pass' : 'fail',
                    $exists ? 'فایل موجود است' : 'فایل پیدا نشد',
                    $path
                );
            } catch (Throwable $e) {
                self::add(
                    'تصاویر تنظیمات',
                    $key,
                    'warn',
                    'خطا در بررسی',
                    $e->getMessage()
                );
            }
        }
    }

    protected static function normalizeUploadedPath(mixed $value): array
    {
        if (is_array($value)) {
            $value = $value[0] ?? null;
        }

        if (! is_string($value) || $value === '') {
            return ['type' => 'empty', 'path' => ''];
        }

        $value = trim($value);

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return ['type' => 'url', 'path' => $value];
        }

        $path = str_replace('\\', '/', $value);
        $path = preg_replace('#^public/#', '', $path) ?? $path;
        $path = preg_replace('#^storage/#', '', $path) ?? $path;
        $path = ltrim($path, '/');

        return ['type' => 'path', 'path' => $path];
    }

    protected static function checkAssets(): void
    {
        $manifestPath = public_path('build/manifest.json');
        $hotPath = public_path('hot');

        if (File::exists($manifestPath)) {
            $manifest = json_decode(File::get($manifestPath), true);
            $count = is_array($manifest) ? count($manifest) : 0;

            self::add(
                '-assets',
                'Vite manifest',
                $count > 0 ? 'pass' : 'warn',
                $count > 0 ? $count . ' entry' : 'خالی یا نامعتبر',
                $manifestPath
            );
        } elseif (File::exists($hotPath)) {
            self::add(
                'assets',
                'Vite manifest',
                'info',
                'حالت dev server فعال است',
                'فایل public/hot وجود دارد. برای production npm run build لازم است.'
            );
        } else {
            self::add(
                'assets',
                'Vite manifest',
                'warn',
                'وجود ندارد',
                'اگر از npm run dev استفاده نمی‌کنی، باید npm run build بزنی.'
            );
        }

        $faviconPath = public_path('images/favicon.png');

        self::add(
            'assets',
            'favicon پیش‌فرض',
            File::exists($faviconPath) ? 'pass' : 'warn',
            File::exists($faviconPath) ? 'موجود' : 'موجود نیست',
            $faviconPath
        );

        $layoutPath = resource_path('views/components/layouts/app.blade.php');

        if (File::exists($layoutPath)) {
            $contents = File::get($layoutPath);

            preg_match(
                '/resources\/js\/app\.(jsx|js)/u',
                $contents,
                $matches
            );

            if (! empty($matches[1])) {
                $entry = 'app.' . $matches[1];
                $entryPath = resource_path('js/' . $entry);

                self::add(
                    'assets',
                    'Vite JS entry در layout',
                    File::exists($entryPath) ? 'pass' : 'fail',
                    $entry,
                    File::exists($entryPath)
                        ? $entryPath
                        : 'layout به ' . $entry . ' اشاره می‌کند ولی فایل وجود ندارد.'
                );
            } else {
                self::add(
                    'assets',
                    'Vite JS entry در layout',
                    'warn',
                    'پیدا نشد',
                    'داخل layout باید @vite([... , resources/js/app.jsx یا app.js]) وجود داشته باشد.'
                );
            }

            preg_match(
                '/resources\/css\/app\.css/u',
                $contents,
                $cssMatches
            );

            self::add(
                'assets',
                'Vite CSS entry در layout',
                ! empty($cssMatches) ? 'pass' : 'warn',
                ! empty($cssMatches) ? 'resources/css/app.css' : 'پیدا نشد'
            );
        }

        $appCss = resource_path('css/app.css');

        self::add(
            'assets',
            'resources/css/app.css',
            File::exists($appCss) ? 'pass' : 'fail',
            File::exists($appCss) ? 'موجود' : 'موجود نیست',
            $appCss
        );

        $appJs = resource_path('js/app.js');
        $appJsx = resource_path('js/app.jsx');

        self::add(
            'assets',
            'resources/js/app.js یا app.jsx',
            File::exists($appJs) || File::exists($appJsx) ? 'pass' : 'fail',
            File::exists($appJsx)
                ? 'app.jsx موجود است'
                : (File::exists($appJs) ? 'app.js موجود است' : 'هیچ‌کدام موجود نیستند')
        );
    }

    protected static function checkSettingsValues(): void
    {
        if (! class_exists(Settings::class)) {
            return;
        }

        $keys = [
            'site.name' => 'نام برند',
            'site.tagline' => 'شعار کوتاه',
            'contact.phone' => 'تلفن',
            'contact.email' => 'ایمیل',
            'contact.address' => 'آدرس',
            'contact.working_hours' => 'ساعات کاری',
            'social.telegram' => 'تلگرام',
            'social.instagram' => 'اینستاگرام',
            'social.whatsapp' => 'واتساپ',
            'social.linkedin' => 'لینکدین',
            'maintenance.message' => 'پیام تعمیرات',
            'maintenance.telegram' => 'تلگرام تعمیرات',
        ];

        foreach ($keys as $key => $label) {
            try {
                $value = Settings::get($key);

                $isEmpty = $value === null
                    || $value === ''
                    || (is_array($value) && $value === []);

                self::add(
                    'تنظیمات',
                    $label . ' (' . $key . ')',
                    $isEmpty ? 'warn' : 'pass',
                    $isEmpty ? 'خالی' : self::truncate((string) json_encode($value, JSON_UNESCAPED_UNICODE), 120)
                );
            } catch (Throwable $e) {
                self::add(
                    'تنظیمات',
                    $key,
                    'warn',
                    'خطا',
                    $e->getMessage()
                );
            }
        }

        try {
            $contactEnabled = (bool) Settings::get('contact.enabled', true);

            self::add(
                'تنظیمات',
                'فرم تماس',
                'info',
                $contactEnabled ? 'فعال' : 'غیرفعال'
            );
        } catch (Throwable $e) {
            self::add(
                'تنظیمات',
                'contact.enabled',
                'warn',
                'خطا',
                $e->getMessage()
            );
        }
    }

    protected static function checkCache(): void
    {
        try {
            $key = 'rendix_diagnostics_' . now()->timestamp;

            Cache::put($key, 'ok', 10);
            $value = Cache::get($key);

            self::add(
                'کش',
                'خواندن/نوشتن Cache',
                $value === 'ok' ? 'pass' : 'fail',
                $value === 'ok' ? 'کار می‌کند' : 'کار نمی‌کند'
            );

            Cache::forget($key);
        } catch (Throwable $e) {
            self::add(
                'کش',
                'خواندن/نوشتن Cache',
                'fail',
                'خطا',
                $e->getMessage()
            );
        }

        self::add(
            'کش',
            'View Compiled',
            'info',
            (string) count(File::glob(storage_path('framework/views/*.php')) ?: [])
        );
    }

    protected static function checkMaintenance(): void
    {
        if (! class_exists(MaintenanceMode::class)) {
            self::add(
                'تعمیرات',
                'کلاس MaintenanceMode',
                'fail',
                'موجود نیست'
            );

            return;
        }

        try {
            $enabled = MaintenanceMode::enabled();

            self::add(
                'تعمیرات',
                'وضعیت حالت تعمیرات',
                $enabled ? 'warn' : 'pass',
                $enabled ? 'روشن است' : 'خاموش است',
                $enabled
                    ? 'کاربران عادی صفحه تعمیرات را می‌بینند. مدیر از پنل می‌تواند خاموش کند.'
                    : 'سایت برای همه عادی باز است.'
            );

            $data = MaintenanceMode::data();

            self::add(
                'تعمیرات',
                'پیام تعمیرات',
                ! empty($data['message']) ? 'pass' : 'warn',
                self::truncate((string) ($data['message'] ?? ''), 120)
            );

            self::add(
                'تعمیرات',
                'لینک تلگرام تعمیرات',
                ! empty($data['telegram']) ? 'pass' : 'warn',
                (string) ($data['telegram'] ?? '')
            );
        } catch (Throwable $e) {
            self::add(
                'تعمیرات',
                'بررسی حالت تعمیرات',
                'fail',
                'خطا',
                $e->getMessage()
            );
        }
    }

    protected static function checkLogs(): void
    {
        $logPath = storage_path('logs/laravel.log');

        if (! File::exists($logPath)) {
            self::add(
                'لاگ‌ها',
                'laravel.log',
                'info',
                'هنوز ساخته نشده',
                $logPath
            );

            return;
        }

        if (! File::isReadable($logPath)) {
            self::add(
                'لاگ‌ها',
                'laravel.log',
                'warn',
                'قابل خواندن نیست',
                $logPath
            );

            return;
        }

        $lines = self::tailFile($logPath, 400);

        $errors = array_values(array_filter($lines, function ($line) {
            return (bool) preg_match('/\.(ERROR|CRITICAL|ALERT|EMERGENCY):/i', (string) $line);
        }));

        $count = count($errors);

        $hint = '';

        if ($count > 0) {
            $latest = array_slice(array_reverse($errors), 0, 3);

            $hint = implode("\n", array_map(
                fn ($line) => self::truncate((string) $line, 220),
                $latest
            ));
        }

        self::add(
            'لاگ‌ها',
            'خطاهای اخیر در laravel.log',
            $count > 0 ? 'warn' : 'pass',
            $count > 0 ? $count . ' خطا در ۴۰ خط آخر' : 'خطایی در ۴۰۰ خط آخر پیدا نشد',
            $hint
        );

        self::add(
            'لاگ‌ها',
            'حجم laravel.log',
            'info',
            self::humanFileSize(File::size($logPath)),
            $logPath
        );
    }

    protected static function checkCurrentUser(): void
    {
        $user = Auth::user();

        if (! $user) {
            self::add(
                'کاربر فعلی',
                'لاگین',
                'warn',
                'کاربری وارد نشده است',
                'این صفحه باید داخل middleware admin.auth باشد.'
            );

            return;
        }

        $isAdmin = method_exists($user, 'isAdmin') && $user->isAdmin();

        self::add(
            'کاربر فعلی',
            'نام',
            'info',
            (string) ($user->name ?? 'بدون نام')
        );

        self::add(
            'کاربر فعلی',
            'ایمیل',
            'info',
            (string) ($user->email ?? '')
        );

        self::add(
            'کاربر فعلی',
            'دسترسی مدیر',
            $isAdmin ? 'pass' : 'fail',
            $isAdmin ? 'بله' : 'خیر'
        );

        self::add(
            'کاربر فعلی',
            'role',
            'info',
            (string) ($user->role ?? 'تنظیم نشده')
        );
    }

    protected static function tailFile(string $path, int $lines = 200): array
    {
        try {
            $file = new \SplFileObject($path, 'r');
            $file->seek(PHP_INT_MAX);

            $lastLine = $file->key();
            $start = max(0, $lastLine - $lines + 1);

            $file->seek($start);

            $result = [];

            for ($i = $start; $i <= $lastLine; $i++) {
                $result[] = $file->current();
                $file->next();
            }

            return $result;
        } catch (Throwable $e) {
            return [];
        }
    }

    protected static function truncate(string $value, int $limit = 160): string
    {
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? '';

        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return mb_substr($value, 0, $limit) . '…';
    }

    protected static function humanFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        $power = min($power, count($units) - 1);

        return round($bytes / (1024 ** $power), 2) . ' ' . $units[(int) $power];
    }

    protected static function statusLabel(string $status): string
    {
        return match ($status) {
            "pass" => "سالم",
            "warn" => "هشدار",
            "fail" => "خراب",
            default => "اطلاعات",
        };
    }

    protected static function buildPlainTextReport(): string
    {
        $hasFail = (self::$summary["fail"] ?? 0) > 0;
        $hasWarn = (self::$summary["warn"] ?? 0) > 0;

        $overallText = $hasFail
            ? "مشکل جدی پیدا شد"
            : ($hasWarn ? "هشدار وجود دارد" : "همه چیز اوکی به نظر می‌رسد");

        $lines = [];

        $lines[] = "گزارش تشخیص سایت رندیکس";
        $lines[] = "تاریخ: " . now()->format("Y-m-d H:i:s");
        $lines[] = "وضعیت کلی: " . $overallText;
        $lines[] = "خلاصه: سالم " . (self::$summary["pass"] ?? 0)
            . " | هشدار " . (self::$summary["warn"] ?? 0)
            . " | خراب " . (self::$summary["fail"] ?? 0)
            . " | اطلاعات " . (self::$summary["info"] ?? 0);
        $lines[] = str_repeat("-", 70);

        foreach (self::$sections as $sectionTitle => $items) {
            $lines[] = "";
            $lines[] = "بخش: " . $sectionTitle;

            foreach ($items as $item) {
                $line = "- " . $item["label"] . ": " . self::statusLabel($item["status"]);

                if (($item["value"] ?? "") !== "") {
                    $line .= " | " . $item["value"];
                }

                $lines[] = $line;

                if (($item["hint"] ?? "") !== "") {
                    foreach (explode("\n", (string) $item["hint"]) as $hintLine) {
                        $hintLine = trim($hintLine);

                        if ($hintLine !== "") {
                            $lines[] = "  " . $hintLine;
                        }
                    }
                }
            }
        }

        return implode("\n", $lines);
    }

    protected static function html(): string
    {
        $total = array_sum(self::$summary);

        $hasFail = (self::$summary["fail"] ?? 0) > 0;
        $hasWarn = (self::$summary["warn"] ?? 0) > 0;

        $overallStatus = $hasFail ? "fail" : ($hasWarn ? "warn" : "pass");
        $overallText = $hasFail
            ? "مشکل جدی پیدا شد"
            : ($hasWarn ? "هشدار وجود دارد" : "همه چیز اوکی به نظر می‌رسد");

        $backUrl = "#";

        try {
            if (Route::has("admin.dashboard")) {
                $backUrl = route("admin.dashboard");
            }
        } catch (Throwable $e) {
            $backUrl = "#";
        }

        if ($backUrl === "#") {
            $prefix = trim((string) config("admin.prefix", "admin"), "/");
            $backUrl = url($prefix !== "" ? $prefix : "/");
        }

        $report = self::buildPlainTextReport();

        $sectionsHtml = "";

        foreach (self::$sections as $sectionTitle => $items) {
            $sectionFail = collect($items)->contains(fn ($item) => $item["status"] === "fail");
            $sectionWarn = collect($items)->contains(fn ($item) => $item["status"] === "warn");

            $open = $sectionFail || $sectionWarn ? "open" : "";

            $rows = "";

            foreach ($items as $item) {
                $statusLabel = self::statusLabel($item["status"]);

                $hint = ($item["hint"] ?? "") !== ""
                    ? '<div class="hint">' . nl2br(e($item["hint"])) . '</div>'
                    : "";

                $rows .= '
                    <tr data-status="' . e($item["status"]) . '">
                        <td>' . e($item["label"]) . '</td>
                        <td><span class="badge badge-' . e($item["status"]) . '">' . e($statusLabel) . '</span></td>
                        <td>' . e($item["value"]) . $hint . '</td>
                    </tr>
                ';
            }

            $sectionsHtml .= '
                <details class="section" ' . $open . '>
                    <summary>
                        <span>' . e($sectionTitle) . '</span>
                        <span class="count">' . count($items) . ' مورد</span>
                    </summary>

                    <table>
                        <thead>
                            <tr>
                                <th>موارد</th>
                                <th>وضعیت</th>
                                <th>جزئیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            ' . $rows . '
                        </tbody>
                    </table>
                </details>
            ';
        }

        return '
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow,noarchive">
    <title>تشخیص سایت رندیکس</title>

    <style>
        :root {
            --bg: #071329;
            --panel: rgba(255,255,255,.055);
            --border: rgba(255,255,255,.10);
            --text: #F7F9FF;
            --muted: rgba(247,249,255,.62);
            --pass: #22c55e;
            --warn: #f59e0b;
            --fail: #fb7185;
            --info: #45C8FF;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Vazirmatn, Tahoma, system-ui, sans-serif;
            background:
                radial-gradient(circle at 15% 15%, rgba(69,200,255,.12), transparent 28%),
                radial-gradient(circle at 85% 10%, rgba(118,83,246,.12), transparent 30%),
                linear-gradient(160deg, #050b18, #071329 48%, #030712);
            color: var(--text);
            padding: 24px;
        }

        .wrap {
            width: min(1200px, 100%);
            margin: 0 auto;
        }

        .hero {
            padding: 24px;
            border-radius: 28px;
            background: linear-gradient(135deg, rgba(255,255,255,.08), rgba(255,255,255,.025));
            border: 1px solid var(--border);
            box-shadow: 0 30px 90px -40px rgba(0,0,0,.9);
            backdrop-filter: blur(18px);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 28px;
            font-weight: 950;
        }

        .sub {
            color: var(--muted);
            line-height: 2;
            margin: 0;
        }

        .summary {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .stat {
            min-width: 120px;
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255,255,255,.04);
            border: 1px solid var(--border);
        }

        .stat strong {
            display: block;
            font-size: 24px;
            margin-bottom: 4px;
        }

        .stat span {
            color: var(--muted);
            font-size: 12px;
            font-weight: 850;
        }

        .stat.pass strong { color: var(--pass); }
        .stat.warn strong { color: var(--warn); }
        .stat.fail strong { color: var(--fail); }
        .stat.info strong { color: var(--info); }

        .overall {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 18px;
            font-weight: 950;
            border: 1px solid var(--border);
            background: rgba(255,255,255,.04);
        }

        .overall.pass { border-color: rgba(34,197,94,.28); color: #bbf7d0; }
        .overall.warn { border-color: rgba(245,158,11,.28); color: #fed7aa; }
        .overall.fail { border-color: rgba(251,113,133,.30); color: #fecdd3; }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 20px 0;
        }

        button,
        .action-btn,
        a.action-btn {
            border: 1px solid var(--border);
            background: rgba(255,255,255,.05);
            color: var(--text);
            border-radius: 999px;
            padding: 10px 14px;
            font-family: inherit;
            font-weight: 900;
            cursor: pointer;
            transition: .2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            line-height: 1;
        }

        button:hover,
        .action-btn:hover,
        a.action-btn:hover {
            border-color: rgba(69,200,255,.35);
            background: rgba(69,200,255,.10);
        }

        .section {
            margin-bottom: 14px;
            border-radius: 24px;
            background: rgba(255,255,255,.035);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        summary {
            list-style: none;
            cursor: pointer;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-weight: 950;
            color: var(--text);
        }

        summary::-webkit-details-marker { display: none; }

        .count {
            color: var(--muted);
            font-size: 12px;
            font-weight: 850;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 18px;
            border-top: 1px solid rgba(255,255,255,.07);
            text-align: right;
            vertical-align: top;
            font-size: 13px;
            line-height: 1.9;
        }

        th {
            color: var(--muted);
            font-size: 12px;
            font-weight: 950;
            background: rgba(255,255,255,.025);
        }

        td:first-child {
            font-weight: 900;
            min-width: 220px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 950;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .badge-pass {
            color: #bbf7d0;
            background: rgba(34,197,94,.10);
            border-color: rgba(34,197,94,.22);
        }

        .badge-warn {
            color: #fed7aa;
            background: rgba(245,158,11,.10);
            border-color: rgba(245,158,11,.22);
        }

        .badge-fail {
            color: #fecdd3;
            background: rgba(251,113,133,.10);
            border-color: rgba(251,113,133,.24);
        }

        .badge-info {
            color: #bae6fd;
            background: rgba(69,200,255,.10);
            border-color: rgba(69,200,255,.22);
        }

        .hint {
            margin-top: 8px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.9;
            word-break: break-word;
        }

        .footer {
            margin-top: 24px;
            color: var(--muted);
            font-size: 12px;
            text-align: center;
        }

        @media (max-width: 760px) {
            body { padding: 14px; }
            .hero { padding: 18px; border-radius: 22px; }
            h1 { font-size: 22px; }
            th, td { padding: 10px 12px; font-size: 12px; }
            td:first-child { min-width: 140px; }
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="hero">
            <h1>گزارش تشخیص خودکار رندیکس</h1>

            <p class="sub">
                این صفحه به‌جای گشتن دستی، وضعیت دیتابیس، ویوها، routeها، استوریج، assets، تنظیمات، لاگ‌ها و امنیت را چک می‌کند.
            </p>

            <div class="summary">
                <div class="stat pass">
                    <strong>' . (self::$summary["pass"] ?? 0) . '</strong>
                    <span>سالم</span>
                </div>

                <div class="stat warn">
                    <strong>' . (self::$summary["warn"] ?? 0) . '</strong>
                    <span>هشدار</span>
                </div>

                <div class="stat fail">
                    <strong>' . (self::$summary["fail"] ?? 0) . '</strong>
                    <span>خراب</span>
                </div>

                <div class="stat info">
                    <strong>' . (self::$summary["info"] ?? 0) . '</strong>
                    <span>اطلاعات</span>
                </div>

                <div class="stat">
                    <strong>' . $total . '</strong>
                    <span>کل موارد</span>
                </div>
            </div>

            <div class="overall ' . e($overallStatus) . '">
                ' . e($overallText) . '
            </div>
        </div>

        <div class="toolbar">
            <a href="' . e($backUrl) . '" class="action-btn">
                بازگشت به پنل
            </a>

            <button type="button" onclick="rxCopyStatus(this)" class="action-btn">
                کپی کل وضعیت
            </button>

            <button type="button" onclick="rxFilter(\"all\")">همه</button>
            <button type="button" onclick="rxFilter(\"fail\")">فقط خراب</button>
            <button type="button" onclick="rxFilter(\"warn\")">فقط هشدار</button>
            <button type="button" onclick="rxFilter(\"pass\")">فقط سالم</button>
            <button type="button" onclick="rxFilter(\"info\")">فقط اطلاعات</button>
            <button type="button" onclick="rxToggleAll(true)">باز کردن همه</button>
            <button type="button" onclick="rxToggleAll(false)">بستن همه</button>
            <button type="button" onclick="window.print()">چاپ / PDF</button>
        </div>

        ' . $sectionsHtml . '

        <div class="footer">
            Rendix Diagnostics — ساخته‌شده برای پیدا کردن سریع مشکلات قبل از deploy
        </div>
    </div>

    <textarea id="rx-status-report" readonly style="position:absolute;left:-9999px;top:-9999px;width:1px;height:1px;opacity:0;">' . e($report) . '</textarea>

    <script>
        function rxFilter(status) {
            document.querySelectorAll("tr[data-status]").forEach((row) => {
                row.style.display = status === "all" || row.dataset.status === status ? "" : "none";
            });
        }

        function rxToggleAll(open) {
            document.querySelectorAll("details.section").forEach((el) => {
                el.open = open;
            });
        }

        function rxCopyStatus(btn) {
            var textarea = document.getElementById("rx-status-report");

            if (!textarea) {
                return;
            }

            var text = textarea.value;
            var original = btn ? btn.textContent : "";

            function markCopied() {
                if (btn) {
                    btn.textContent = "کپی شد ✓";

                    setTimeout(function () {
                        btn.textContent = original;
                    }, 1800);
                }
            }

            function fallbackCopy() {
                textarea.focus();
                textarea.select();
                textarea.setSelectionRange(0, 999999);

                try {
                    var ok = document.execCommand("copy");

                    if (ok) {
                        markCopied();
                    } else {
                        alert("کپی نشد. متن را دستی کپی کن.");
                    }
                } catch (err) {
                    alert("کپی نشد. متن را دستی کپی کن.");
                }
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text)
                    .then(markCopied)
                    .catch(fallbackCopy);
            } else {
                fallbackCopy();
            }
        }
    </script>
</body>
</html>
        ';
    }
}