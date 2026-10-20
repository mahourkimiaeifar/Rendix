<?php

namespace App\Http\Middleware;

use App\Support\MaintenanceMode;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RenderMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        if (! MaintenanceMode::enabled()) {
            return $next($request);
        }

        $prefix = trim((string) config('admin.prefix', 'admin'), '/');

        $ignoredPaths = [
            'up',
            '_health',
            'sanctum/csrf-cookie',
        ];

        foreach ($ignoredPaths as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        if ($prefix !== '') {
            if ($request->is($prefix) || $request->is($prefix . '/*')) {
                return $next($request);
            }
        }

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