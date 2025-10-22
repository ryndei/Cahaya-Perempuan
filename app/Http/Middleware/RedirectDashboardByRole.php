<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectDashboardByRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            // Pastikan user login dulu
            return redirect()->route('login');
        }

        // Jika user admin/super-admin dan sedang menuju route 'dashboard',
        // arahkan ke halaman admin.
        if ($user->hasAnyRole(['admin', 'super-admin']) && $request->routeIs('dashboard')) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
