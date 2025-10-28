<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

use App\Models\Complaint;
use App\Policies\ComplaintPolicy;

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
        /** ---------------------------
         *  1) Policy binding
         * -------------------------- */
        Gate::policy(Complaint::class, ComplaintPolicy::class);

        // Super Admin (Spatie) bypass semua policy/ability
        Gate::before(function ($user, string $ability) {
            return $user && method_exists($user, 'hasRole') && $user->hasRole('super-admin')
                ? true
                : null;
        });

        /** ---------------------------
         *  2) Locale tanggal (Carbon)
         * -------------------------- */
        $locale = config('app.locale', 'id');
        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);
        @setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian', 'id');

        /** ---------------------------
         *  3) Rate limiter khusus form pengaduan
         * -------------------------- */
        if (app()->bound('cache.store')) {
            RateLimiter::for('complaints', function ($request) {
                $key = optional($request->user())->id ?: $request->ip();
                return Limit::perHour(5)->by($key);
            });
        }

        /** ---------------------------
         *  4) Kustom URL & email reset password (Breeze/Fortify)
         * -------------------------- */
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            return route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });

        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);

            return (new MailMessage)
                ->subject('Reset Password - ' . config('app.name'))
                ->greeting('Halo,')
                ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda.')
                ->action('Atur Ulang Kata Sandi', $url)
                ->line('Link ini akan kedaluwarsa dalam ' . (int) config('auth.passwords.users.expire', 60) . ' menit.')
                ->line('Jika Anda tidak meminta reset, abaikan email ini.');
        });
    }
}
