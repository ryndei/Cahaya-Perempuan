<x-guest-layout>
  <div class="w-full max-w-md">
    {{-- Brand kecil --}}
    <div class="mx-auto mb-4 w-max">
      <a href="/" class="inline-flex items-center gap-3 select-none group">
        <img src="{{ asset('asset/Logo.png') }}" alt="Cahaya Perempuan" class="h-8 w-auto object-contain">
        <span class="text-lg font-extrabold">
          <span class="text-purple-900 group-hover:text-purple-700 transition-colors">Cahaya</span>
          <span class="text-amber-700 group-hover:text-amber-600 transition-colors">Perempuan</span>
        </span>
      </a>
    </div>

    {{-- Kartu Register --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      {{-- Strip gradien --}}
      <div class="h-2 w-full bg-gradient-to-r from-purple-700 via-purple-600 to-amber-600"></div>

      <div class="p-6 sm:p-8" x-data="{ show1:false, show2:false }">
        <header class="mb-6">
          <h1 class="text-2xl font-extrabold text-slate-900">Daftar</h1>
          <p class="mt-1 text-sm text-slate-600">
            Buat akun untuk mengakses layanan secara aman dan rahasia.
          </p>
        </header>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
          @csrf

          {{-- Nama --}}
          <div>
            <x-input-label for="name" :value="__('Name')" />
            <div class="relative mt-1">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                {{-- icon user --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 2a5 5 0 1 1 0 10a5 5 0 0 1 0-10m0 12c-5.33 0-8 2.67-8 6v2h16v-2c0-3.33-2.67-6-8-6Z"/>
                </svg>
              </span>
              <x-text-input
                id="name" name="name" type="text" :value="old('name')" required autofocus autocomplete="name"
                class="block w-full pl-10 focus:border-purple-600 focus:ring-purple-600"
              />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
          </div>

          {{-- Email --}}
          <div>
            <x-input-label for="email" :value="__('Email')" />
            <div class="relative mt-1">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2m0 4-8 5L4 8V6l8 5l8-5z"/>
                </svg>
              </span>
              <x-text-input
                id="email" name="email" type="email" :value="old('email')" required autocomplete="username"
                class="block w-full pl-10 focus:border-purple-600 focus:ring-purple-600"
              />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
          </div>

          {{-- Password --}}
          <div>
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 1a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2h-1V6a5 5 0 0 0-5-5m3 8H9V6a3 3 0 0 1 6 0z"/>
                </svg>
              </span>
              <x-text-input
                id="password" name="password" type="password" required autocomplete="new-password"
                class="block w-full pl-10 pr-10 focus:border-purple-600 focus:ring-purple-600"
                x-bind:type="show1 ? 'text' : 'password'"
              />
              <button type="button" @click="show1 = !show1"
                      class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600"
                      aria-label="Tampilkan/Sembunyikan password">
                <svg x-show="!show1" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7m0 12a5 5 0 1 1 0-10a5 5 0 0 1 0 10Z"/></svg>
                <svg x-show="show1" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M2 5.27L3.28 4L20 20.72L18.73 22l-2.4-2.4c-1.25.26-2.22.35-3.33.35C6 20 3 13 3 13a17.4 17.4 0 0 1 4.19-5.74M12 7c6 0 9 6 9 6a17.4 17.4 0 0 1-2.22 3.63l-2.86-2.86A5 5 0 0 0 9.23 9.18l-1.7-1.7C9 7.17 10.39 7 12 7Z"/></svg>
              </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
          </div>

          {{-- Konfirmasi Password --}}
          <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="relative mt-1">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 1a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2h-1V6a5 5 0 0 0-5-5m3 8H9V6a3 3 0 0 1 6 0z"/>
                </svg>
              </span>
              <x-text-input
                id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                class="block w-full pl-10 pr-10 focus:border-purple-600 focus:ring-purple-600"
                x-bind:type="show2 ? 'text' : 'password'"
              />
              <button type="button" @click="show2 = !show2"
                      class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600"
                      aria-label="Tampilkan/Sembunyikan konfirmasi">
                <svg x-show="!show2" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7m0 12a5 5 0 1 1 0-10a5 5 0 0 1 0 10Z"/></svg>
                <svg x-show="show2" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M2 5.27L3.28 4L20 20.72L18.73 22l-2.4-2.4c-1.25.26-2.22.35-3.33.35C6 20 3 13 3 13a17.4 17.4 0 0 1 4.19-5.74M12 7c6 0 9 6 9 6a17.4 17.4 0 0 1-2.22 3.63l-2.86-2.86A5 5 0 0 0 9.23 9.18l-1.7-1.7C9 7.17 10.39 7 12 7Z"/></svg>
              </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
          </div>

          {{-- Submit --}}
          <button type="submit"
                  class="inline-flex w-full justify-center rounded-lg bg-purple-700 px-4 py-2.5 font-semibold text-white hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-400/60">
            {{ __('Register') }}
          </button>
        </form>

        {{-- Divider & CTA Login --}}
        <div class="mt-6 flex items-center gap-3 text-xs text-slate-400">
          <div class="h-px w-full bg-slate-200"></div>
          <span>atau</span>
          <div class="h-px w-full bg-slate-200"></div>
        </div>
        <p class="mt-4 text-center text-sm text-slate-700">
          Sudah punya akun?
          <a href="{{ route('login') }}" class="font-semibold text-purple-700 hover:text-purple-800">
            Masuk di sini
          </a>
        </p>
      </div>
    </div>
  </div>
</x-guest-layout>
