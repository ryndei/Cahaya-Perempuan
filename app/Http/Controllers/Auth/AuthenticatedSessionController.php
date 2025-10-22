<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
         $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        // Fallback tujuan setelah login (jika tidak ada URL intended)
        $fallback = ($user && $user->hasAnyRole(['admin','super-admin']))
            ? route('admin.dashboard', absolute: false)   // contoh: /admin
            : route('dashboard', absolute: false);        // contoh: /dashboard (user)

        // Jika user sebelumnya akses halaman terproteksi, intended() akan mengarahkan ke sana.
        // Kalau tidak ada intended, pakai $fallback di atas.
        return redirect()->intended($fallback);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
