<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;             
use Illuminate\Http\Request;                   
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
        /**
         * 1) Policy binding + super-admin bypass (Spatie)
         * ------------------------------------------------
         * Kamu boleh taruh binding policy di AuthServiceProvider,
         * tapi di sini juga valid selama dipanggil saat boot.
         */
        Gate::policy(Complaint::class, ComplaintPolicy::class);

        Gate::before(function ($user, string $ability) {
            return $user
                && method_exists($user, 'hasRole')
                && $user->hasRole('super-admin')
                ? true
                : null;
        });

        /**
         * 2) Locale tanggal (Carbon)
         * ------------------------------------------------
         * @setlocale disilent karena environment (Windows/Linux) bisa beda.
         */
        $locale = config('app.locale', 'id');
        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);
        @setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian', 'id');

        /**
         * 3) Rate limiter khusus form pengaduan
         * ------------------------------------------------
         * Sesuaikan dengan middleware routes: throttle:complaints
         * Pakai perMinute(5) agar match rekomendasi sebelumnya.
         */
        RateLimiter::for('complaints', function (Request $request) {
            $key = optional($request->user())->id ?: $request->ip();
            return Limit::perHour(5)->by($key);
        });

        /**
         * 4) Kustom URL & email reset password (Breeze/Fortify)
         * ------------------------------------------------
         * Pastikan route('password.reset') ada (Breeze/Fortify default).
         */
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

            $expire = (int) config('auth.passwords.users.expire', 60);

            return (new MailMessage)
                ->subject('Reset Password - ' . config('app.name'))
                ->greeting('Halo,')
                ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda.')
                ->action('Atur Ulang Kata Sandi', $url)
                ->line("Link ini akan kedaluwarsa dalam {$expire} menit.")
                ->line('Jika Anda tidak meminta reset, abaikan email ini.');
        });

        if (app()->isProduction() && config('app.url') && str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
