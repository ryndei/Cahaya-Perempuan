<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmailOtpController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required','digits:6']
        ]);

        $user = $request->user();
        $record = EmailOtp::where('user_id', $user->id)->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Kode tidak ditemukan. Silakan kirim ulang.']);
        }

        // batas percobaan sederhana
        if ($record->attempts >= 5) {
            return back()->withErrors(['otp' => 'Terlalu banyak percobaan. Kirim ulang kode.']);
        }

        $record->increment('attempts');

        if (now()->greaterThan($record->expires_at)) {
            return back()->withErrors(['otp' => 'Kode sudah kedaluwarsa. Kirim ulang kode.']);
        }

        if (! Hash::check($request->otp, $record->code_hash)) {
            return back()->withErrors(['otp' => 'Kode salah. Silakan coba lagi.']);
        }

        // Berhasil → verifikasi email & hapus OTP
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        $record->delete();

        return redirect()->route('dashboard')->with('status', 'Email berhasil diverifikasi.');
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        // generate OTP 6 digit
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailOtp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'code_hash'  => Hash::make($otp),
                'attempts'   => 0,
                'expires_at' => now()->addMinutes(10),
            ]
        );

        $user->notify(new \App\Notifications\SendEmailOtp($otp, 10));

        return back()->with('status', 'Kode verifikasi telah dikirim ulang.');
    }
}
