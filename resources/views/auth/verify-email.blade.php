<x-guest-layout>
  <div class="min-h-screen bg-gradient-to-b from-violet-50 to-white flex items-center justify-center py-12 px-4">
    <div class="relative w-full max-w-3xl">

      {{-- Avatar gradien --}}
      <div class="absolute inset-x-0 -top-10 flex justify-center">
        <div class="h-20 w-20 rounded-full bg-gradient-to-tr from-fuchsia-500 via-pink-500 to-indigo-500 p-[3px] shadow-xl">
          <div class="h-full w-full rounded-full bg-white flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9 text-indigo-500" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 12a5 5 0 1 0-5-5a5 5 0 0 0 5 5m0 2c-5.33 0-8 2.667-8 5v1a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-1c0-2.333-2.67-5-8-5"/>
            </svg>
          </div>
        </div>
      </div>

      {{-- Kartu --}}
      <div class="rounded-3xl bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white shadow-2xl ring-1 ring-black/5 px-6 py-8 sm:px-10 sm:py-12">
        <div class="pt-8 text-center">
          <h1 class="text-3xl font-semibold tracking-tight text-slate-900">Verifikasi Email</h1>
          <p class="mt-2 text-sm text-slate-600">
            Kami telah mengirimkan <span class="font-medium">kode OTP 6 digit</span> ke email kamu.
            Masukkan kode di bawah. Kode berlaku <span class="font-medium">10 menit</span>.
          </p>
        </div>

        @if (session('status'))
          <div class="mt-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
          </div>
        @endif

        @error('otp')
          <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $message }}
          </div>
        @enderror

        {{-- FORM OTP (hanya field, tanpa tombol) --}}
        <div class="mt-8">
          <div class="flex items-center justify-between">
            <label class="block text-sm font-medium text-slate-700">Kode OTP</label>
            <span class="text-xs text-slate-500">Gunakan kode yang dikirim</span>
          </div>

          <form id="otpForm" method="POST" action="{{ route('verification.otp.verify') }}">
            @csrf
            <div class="mt-2 flex items-center rounded-2xl ring-1 ring-slate-200 bg-white shadow-sm focus-within:ring-2 focus-within:ring-indigo-500">
              <input
                type="text"
                name="otp"
                inputmode="numeric"
                autocomplete="one-time-code"
                pattern="\d{6}"
                maxlength="6"
                class="w-full rounded-2xl border-0 px-4 py-3 text-lg tracking-[0.6em] text-center placeholder-slate-400 focus:outline-none"
                placeholder="••••••"
                required
              />
            </div>
          </form>

          <p class="mt-2 text-xs text-slate-500">
            Tidak menerima email? Periksa folder <span class="italic">Spam/Junk</span> atau kirim ulang kode di bawah.
          </p>
        </div>

        {{-- BARIS TOMBOL: kiri = kirim ulang (menempel kiri), kanan = kembali + verifikasi --}}
        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          {{-- KIRI: Kirim Ulang Kode --}}
          <form method="POST" action="{{ route('verification.otp.resend') }}" class="sm:mr-auto">
            @csrf
            <button type="submit"
              class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-6 py-3 text-sm font-medium text-slate-800 hover:bg-slate-200">
              Kirim Ulang Kode
            </button>
          </form>

          {{-- KANAN: Kembali & Verifikasi --}}
          <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
              Kembali ke Masuk
            </a>

            {{-- tombol submit form OTP dari luar form --}}
            <button type="submit" form="otpForm"
              class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:from-violet-700 hover:to-fuchsia-700 focus:outline-none focus:ring-2 focus:ring-violet-500">
              Verifikasi
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</x-guest-layout>
