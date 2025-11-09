<?php

use Illuminate\Support\Facades\Route;

// Auth controllers (Breeze)
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;

// OTP controller
use App\Http\Controllers\Auth\EmailOtpController;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    /**
     * Email Verification via OTP
     * Halaman input OTP + verifikasi + kirim ulang
     */
    Route::get('verify-email', function () {
        return view('auth.verify-email'); 
    })->name('verification.notice');

    Route::post('verify-email/otp', [EmailOtpController::class, 'verify'])
        ->middleware('throttle:5,1') 
        ->name('verification.otp.verify');

    Route::post('verify-email/otp/resend', [EmailOtpController::class, 'resend'])
        ->middleware('throttle:3,1') 
        ->name('verification.otp.resend');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});