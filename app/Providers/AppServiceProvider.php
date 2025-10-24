<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
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

        // Opsional: PHP locale untuk nama bulan/hari pada fungsi non-Carbon
        @setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian', 'id');
    }
}
