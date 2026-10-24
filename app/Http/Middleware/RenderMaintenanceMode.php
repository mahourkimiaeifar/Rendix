<?php

namespace App\Http\Middleware;

use App\Support\MaintenanceMode;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RenderMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! MaintenanceMode::enabled()) {
            return $next($request);
        }

        $prefix = trim((string) config('admin.prefix', 'admin'), '/');

        /*
        |--------------------------------------------------------------------------
        | Static / asset / health paths
        |--------------------------------------------------------------------------
        */
        $ignoredPaths = [
            'up',
            '_health',
            'sanctum/csrf-cookie',
            'storage/*',
            'build/*',
            'images/*',
            'favicon.ico',
            'robots.txt',
            'sitemap.xml',
        ];

        foreach ($ignoredPaths as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Admin panel paths
        |--------------------------------------------------------------------------
        |
        | همه چیز زیر پیشوند پنل مدیریت باید از حالت تعمیرات مستثنا باشد،
        | شامل login, dashboard, leads, settings, services, projects, ...
        |
        */
        if ($prefix !== '') {
            if (
                $request->is($prefix) ||
                $request->is($prefix . '/*')
            ) {
                return $next($request);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Public auth + customer dashboard paths
        |--------------------------------------------------------------------------
        */
        $authPaths = [
            'login',
            'register',
            'logout',
            'dashboard',
            'dashboard/*',
        ];

        foreach ($authPaths as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Route-name based bypass
        |--------------------------------------------------------------------------
        */
        $route = $request->route();

        if ($route) {
            $routeName = (string) ($route->getName() ?? '');

            $bypassRouteNames = [
                'login',
                'register',
                'logout',
                'dashboard',
                'customer.dashboard',
                'customer.requests',
                'customer.requests.show',
                'customer.profile',
            ];

            if (
                $routeName !== '' &&
                (
                    str_starts_with($routeName, 'admin.') ||
                    in_array($routeName, $bypassRouteNames, true)
                )
            ) {
                return $next($request);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Livewire internal requests
        |--------------------------------------------------------------------------
        |
        | Livewire فرم‌ها را معمولاً به /livewire/update می‌فرستد.
        | اگر مبدا/رفرر آن صفحه لاگین، پنل، یا داشبورد مشتری باشد،
        | نباید حالت تعمیرات جلوی آن را بگیرد.
        |
        */
        if (
            $request->is('livewire/*') ||
            $request->is('livewire/update') ||
            $request->is('livewire/message/*')
        ) {
            $referer = (string) $request->header('referer', '');
            $refererPath = (string) (parse_url($referer, PHP_URL_PATH) ?? '');
            $refererPath = trim($refererPath, '/');

            $allowedRefererPaths = array_values(array_filter([
                $prefix,
                'login',
                'register',
                'logout',
                'dashboard',
            ]));

            foreach ($allowedRefererPaths as $allowedPath) {
                if ($allowedPath === '') {
                    continue;
                }

                if (
                    $refererPath === $allowedPath ||
                    str_starts_with($refererPath, $allowedPath . '/')
                ) {
                    return $next($request);
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Authenticated admin bypass
        |--------------------------------------------------------------------------
        |
        | اگر مدیر لاگین کرده باشد، حتی در حالت تعمیرات هم باید
        | به پنل و صفحات مدیریتی دسترسی داشته باشد.
        |
        */
        $user = Auth::user();

        if (
            $user &&
            method_exists($user, 'isAdmin') &&
            $user->isAdmin()
        ) {
            return $next($request);
        }

        return response()
            ->view('maintenance', [
                'maintenance' => MaintenanceMode::data(),
            ], 503)
            ->header('Retry-After', '3600');
    }
}