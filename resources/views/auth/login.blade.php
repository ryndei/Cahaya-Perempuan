<x-guest-layout>
  <div class="min-h-screen bg-purple-50 flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-md">
      {{-- Header Brand kecil --}}
      <div class="mx-auto mb-4 w-max">
        <a href="/" class="inline-flex items-center gap-3 select-none group">
          <img src="{{ asset('asset/Logo.png') }}" alt="Cahaya Perempuan" class="h-8 w-auto object-contain">
          <span class="text-lg font-extrabold">
            <span class="text-purple-900 group-hover:text-purple-700 transition-colors">Cahaya</span>
            <span class="text-amber-700 group-hover:text-amber-600 transition-colors">Perempuan</span>
          </span>
        </a>
      </div>

      {{-- Kartu Login --}}
      <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        {{-- Strip warna atas (tone ungu→amber) --}}
        <div class="h-2 w-full bg-gradient-to-r from-purple-700 via-purple-600 to-amber-600"></div>

        <div class="p-6 sm:p-8">
          <header class="mb-6">
            <h1 class="text-2xl font-extrabold text-slate-900">Masuk</h1>
            <p class="mt-1 text-sm text-slate-600">
              Selamat datang kembali. Silakan masuk untuk mengakses layanan.
            </p>
          </header>

          {{-- Session Status --}}
          <x-auth-session-status class="mb-4" :status="session('status')" />

          <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ show: false }">
            @csrf

            {{-- Email --}}
            <div>
              <x-input-label for="email" :value="__('Email')" />
              <div class="relative mt-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                  <!-- Icon mail -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2m0 4-8 5L4 8V6l8 5l8-5z"/></svg>
                </span>
                <x-text-input
                  id="email"
                  type="email"
                  name="email"
                  :value="old('email')"
                  required
                  autofocus
                  autocomplete="username"
                  class="block w-full pl-10 focus:border-purple-600 focus:ring-purple-600"
                />
              </div>
              <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Password + Forgot --}}
            <div>
              <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                  <a href="{{ route('password.request') }}"
                     class="text-sm font-medium text-purple-700 hover:text-purple-800">
                    {{ __('Forgot your password?') }}
                  </a>
                @endif
              </div>

              <div class="relative mt-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                  <!-- Icon lock -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2h-1V6a5 5 0 0 0-5-5m3 8H9V6a3 3 0 0 1 6 0z"/></svg>
                </span>

                <x-text-input
                  x-bind:type="show ? 'text' : 'password'"
                  id="password"
                  name="password"
                  required
                  autocomplete="current-password"
                  class="block w-full pl-10 pr-10 focus:border-purple-600 focus:ring-purple-600"
                />

                {{-- Toggle show/hide --}}
                <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600"
                        aria-label="Toggle password visibility">
                  <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7m0 12a5 5 0 1 1 0-10a5 5 0 0 1 0 10Z"/></svg>
                  <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M2 5.27L3.28 4L20 20.72L18.73 22l-2.4-2.4c-1.25.26-2.22.35-3.33.35C6 20 3 13 3 13a17.4 17.4 0 0 1 4.19-5.74M12 7c6 0 9 6 9 6a17.4 17.4 0 0 1-2.22 3.63l-2.86-2.86A5 5 0 0 0 9.23 9.18l-1.7-1.7C9 7.17 10.39 7 12 7Z"/></svg>
                </button>
              </div>

              <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Remember --}}
            <label for="remember_me" class="flex items-center gap-2 select-none">
              <input id="remember_me" name="remember" type="checkbox"
                     class="h-4 w-4 rounded border-slate-300 text-purple-700 focus:ring-purple-600">
              <span class="text-sm text-slate-700">{{ __('Remember me') }}</span>
            </label>

            {{-- Submit --}}
            <button type="submit"
              class="inline-flex w-full justify-center rounded-lg bg-purple-700 px-4 py-2.5 font-semibold text-white hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-400/60">
              {{ __('Log in') }}
            </button>
          </form>

          {{-- Divider --}}
          <div class="mt-6 flex items-center gap-3 text-xs text-slate-400">
            <div class="h-px w-full bg-slate-200"></div>
            <span>atau</span>
            <div class="h-px w-full bg-slate-200"></div>
          </div>

          {{-- CTA Register --}}
          @if (Route::has('register'))
            <p class="mt-4 text-center text-sm text-slate-700">
              Belum punya akun?
              <a href="{{ route('register') }}" class="font-semibold text-purple-700 hover:text-purple-800">
                Daftar sekarang
              </a>
            </p>
          @endif
        </div>
      </div>

      {{-- Footnote kecil --}}
      <p class="mt-6 text-center text-xs text-slate-500">
        Berperspektif korban • Rahasia • Aman
      </p>
    </div>
  </div>
</x-guest-layout>
