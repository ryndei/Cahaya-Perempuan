<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Super Admin bypass semua ability/policy
        Gate::before(function ($user, $ability) {
            return $user && method_exists($user, 'hasRole') && $user->hasRole('super-admin')
                ? true
                : null;
        });

        // Paksa Carbon pakai locale aplikasi (contoh: 'id')
        $locale = config('app.locale', 'id');
        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);
        @setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian', 'id');

        // Registrasi RateLimiter secara aman:
        // hanya saat binding cache.store sudah tersedia agar tidak meledak saat artisan/bootstrap awal.
        if (app()->bound('cache.store')) {
            RateLimiter::for('complaints', function ($request) {
                return Limit::perMinute(3)->by(optional($request->user())->id ?: $request->ip());
            });
        }
    }
}
