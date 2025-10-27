<x-guest-layout>
  {{-- Latar lembut ala contoh --}}
  <div class="min-h-screen w-full bg-violet-50/80 py-10 px-4 flex items-center justify-center">
    <div class="w-full max-w-3xl">

      {{-- CARD UTAMA --}}
      <div class="relative mx-auto rounded-3xl bg-white border border-slate-200 shadow-xl p-6 sm:p-8 md:p-10">

        {{-- Badge ikon gradasi di atas card --}}
        <div class="absolute left-1/2 -top-8 -translate-x-1/2">
          <div class="h-16 w-16 rounded-full bg-gradient-to-b from-fuchsia-500 to-orange-400 shadow-lg ring-4 ring-white/70 grid place-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 11a3 3 0 1 0-3-3 3 3 0 0 0 3 3ZM19 21v-1a6 6 0 0 0-12 0v1"/>
            </svg>
          </div>
        </div>

        {{-- Judul & deskripsi --}}
        <div class="pt-6 text-center">
          <h1 class="text-xl sm:text-2xl font-semibold text-slate-900">Lupa Kata Sandi?</h1>
          <p class="mt-2 text-sm text-slate-600 leading-relaxed">
            Masukkan alamat email yang terdaftar. Kami akan mengirimkan tautan aman untuk mengatur ulang kata sandi Anda.
          </p>
        </div>

        {{-- Status dari Breeze (sukses kirim tautan) --}}
        <x-auth-session-status
          class="mt-5 mb-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm"
          :status="session('status')" />

        {{-- FORM --}}
        <form id="forgot-form" method="POST" action="{{ route('password.email') }}" class="mt-4">
          @csrf

          {{-- “Card” input ala contoh: putih, ring tipis, radius besar --}}
          <div class="rounded-2xl border border-slate-200 bg-white/95 p-4 sm:p-5">
            <label for="email" class="flex items-center justify-between text-sm font-medium text-slate-700">
              <span>Email</span>
              <span class="text-[11px] font-normal text-slate-500">Gunakan email yang terdaftar</span>
            </label>

            <x-text-input
              id="email"
              type="email"
              name="email"
              :value="old('email')"
              required
              autofocus
              autocomplete="email"
              placeholder="nama@contoh.com"
              class="mt-2 block w-full text-[15px]"
            />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />

            {{-- Tips kecil ala catatan --}}
            <p class="mt-3 text-xs text-slate-500">
              Tidak menerima email? Periksa folder <em>Spam/Junk</em> atau pastikan alamat email benar.
            </p>
          </div>

          {{-- Aksi (tombol ungu + link kembali) --}}
          <div class="mt-5 flex flex-col sm:flex-row sm:items-center sm:justify-center gap-3">
            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
              Kembali ke Masuk
            </a>
            <button type="submit" id="submit-btn"
              class="inline-flex items-center justify-center gap-2 rounded-xl bg-purple-700 px-5 py-2.5 text-sm font-semibold text-white
                     shadow-md hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-400/60 focus:ring-offset-2
                     disabled:opacity-60 disabled:cursor-not-allowed">
              <svg id="btn-spinner" class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z"/>
              </svg>
              <span id="btn-text">Kirim Tautan Reset</span>
            </button>
          </div>
        </form>

        {{-- Catatan kaki ala contoh --}}
        <p class="mt-6 text-center text-[12px] text-slate-500">
          Setelah menerima email, klik tautan yang dikirim untuk membuat kata sandi baru.
        </p>
      </div>
    </div>
  </div>

  {{-- Spinner & cegah double submit --}}
  <script>
    (() => {
      const form = document.getElementById('forgot-form');
      const btn = document.getElementById('submit-btn');
      const spinner = document.getElementById('btn-spinner');
      const btnText = document.getElementById('btn-text');

      if (!form) return;
      form.addEventListener('submit', () => {
        btn.disabled = true;
        spinner?.classList.remove('hidden');
        if (btnText) btnText.textContent = 'Mengirim…';
      });
    })();
  </script>
</x-guest-layout>
