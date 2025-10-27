<x-guest-layout>
  <div class="relative mx-auto rounded-3xl bg-white border border-slate-200 shadow-xl py-10 px-4">
    <div class="w-full max-w-md">
      {{-- Brand / Judul --}}
      <div class="text-center mb-6">
        <div class="mx-auto h-12 w-12 rounded-xl bg-indigo-600/10 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v3m-6 4h12a2 2 0 002-2v-7a8 8 0 10-16 0v7a2 2 0 002 2z"/>
          </svg>
        </div>
        <h1 class="mt-3 text-xl font-semibold text-slate-900">Atur Ulang Kata Sandi</h1>
        <p class="mt-1 text-sm text-slate-600">Masukkan email dan kata sandi baru Anda.</p>
      </div>

      {{-- Card --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
          @csrf

          <!-- Password Reset Token -->
          <input type="hidden" name="token" value="{{ $request->route('token') }}">

          <!-- Email Address -->
          <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
              id="email"
              class="block mt-1 w-full"
              type="email"
              name="email"
              :value="old('email', $request->email)"
              required
              autofocus
              autocomplete="username"
              placeholder="nama@contoh.com"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
          </div>

          <!-- Password -->
          <div>
            <div class="flex items-center justify-between">
              <x-input-label for="password" :value="__('Kata Sandi Baru')" />
              <span class="text-xs text-slate-500">min. 8 karakter</span>
            </div>

            <div class="relative">
              <x-text-input
                id="password"
                class="block mt-1 w-full pr-10"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="••••••••"
              />
              <button type="button"
                      class="absolute inset-y-0 right-0 mt-1 mr-2 inline-flex items-center justify-center rounded-md p-2 text-slate-500 hover:text-slate-700 focus:outline-none"
                      aria-label="Tampilkan/samarkan kata sandi"
                      data-toggle-password="#password">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
          </div>

          <!-- Confirm Password -->
          <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
            <div class="relative">
              <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full pr-10"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="••••••••"
              />
              <button type="button"
                      class="absolute inset-y-0 right-0 mt-1 mr-2 inline-flex items-center justify-center rounded-md p-2 text-slate-500 hover:text-slate-700 focus:outline-none"
                      aria-label="Tampilkan/samarkan konfirmasi kata sandi"
                      data-toggle-password="#password_confirmation">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
          </div>

          <div class="pt-2 flex items-center justify-between gap-3">
            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
              Kembali ke masuk
            </a>

            <x-primary-button class="inline-flex items-center justify-center gap-2 rounded-xl bg-purple-700 px-5 py-2.5 text-sm font-semibold text-white
                     shadow-md hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-400/60 focus:ring-offset-2
                     disabled:opacity-60 disabled:cursor-not-allowed">
              {{-- icon --}}
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -ml-1" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0l-4-4m4 4l4-4M5 20h14"/>
              </svg>
              {{ __('Reset Password') }}
            </x-primary-button>
          </div>
        </form>
      </div>

      {{-- Footer kecil --}}
      <p class="mt-4 text-center text-xs text-slate-500">
        Jika tidak meminta reset, abaikan email ini.
      </p>
    </div>
  </div>

  {{-- Toggle show/hide password (vanilla JS, tidak butuh Alpine) --}}
  <script>
    document.querySelectorAll('[data-toggle-password]').forEach(btn => {
      btn.addEventListener('click', () => {
        const input = document.querySelector(btn.getAttribute('data-toggle-password'));
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
        // optional: toggle icon "eye/eye-off" jika mau
      });
    });
  </script>
</x-guest-layout>
