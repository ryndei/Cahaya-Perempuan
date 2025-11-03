<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailOtp;
use App\Notifications\SendEmailOtp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Tampilkan halaman registrasi.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Proses pendaftaran user + kirim OTP verifikasi email.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Role default
        $user->assignRole('user');

        // Login agar dapat akses halaman verifikasi
        Auth::login($user);

        // === Generate & kirim OTP (6 digit, berlaku 10 menit) ===
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailOtp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'code_hash'  => Hash::make($otp),
                'attempts'   => 0,
                'expires_at' => now()->addMinutes(10),
            ]
        );

        $user->notify(new SendEmailOtp($otp, 10));

        // Arahkan ke halaman input OTP
        return redirect()
            ->route('verification.notice')
            ->with('status', 'Kode verifikasi telah dikirim ke email Anda.');
    }
}
