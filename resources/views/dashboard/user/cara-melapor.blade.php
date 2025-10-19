
<x-app-layout>
  {{-- Top menu --}}
 <x-slot name="header">
    @include('profile.partials.top-menu')
    </x-slot>

  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Tumpukan/stacked paper --}}
    <div class="relative">
      {{-- lembar bayangan 1 --}}
      <div
        class="pointer-events-none absolute -top-3 left-6 right-6 h-full rounded-2xl bg-slate-100 shadow-sm rotate-[-2deg]">
      </div>
      {{-- lembar bayangan 2 --}}
      <div
        class="pointer-events-none absolute -top-1 left-3 right-3 h-full rounded-2xl bg-slate-100 shadow-sm rotate-[1.5deg]">
      </div>

      {{-- Lembar utama --}}
      <article class="relative rounded-2xl bg-white border border-slate-200 shadow-sm mt-2">
        <header class="px-6 sm:px-8 pt-7 pb-4">
          <h1 class="text-3xl font-black tracking-tight text-slate-800">
            CARA MELAPOR
          </h1>
          <div class="mt-3 h-0.5 w-28 bg-purple-600/80 rounded-full"></div>
        </header>

        <div class="px-6 sm:px-8 pb-8">
          <ol class="list-decimal list-inside space-y-6 text-slate-800 leading-relaxed">
            <li>
              Klik tombol
              <a href="{{ route('login') }}"
                 class="font-semibold text-purple-700 hover:text-purple-800 underline underline-offset-2">
                Login
              </a>,
              lalu isi <strong>email</strong> dan <strong>kata sandi</strong> Anda.
            </li>

            <li>
              Belum punya akun? Klik
              <a href="{{ route('register') }}"
                 class="font-semibold text-purple-700 hover:text-purple-800 underline underline-offset-2">
                Register
              </a>,
              lengkapi data diri, lalu klik <strong>"Simpan"</strong>.
              <ul class="mt-2 ms-6 list-disc space-y-1 text-slate-700">
                <li>Gunakan <strong>nama samaran</strong> bila perlu.</li>
                <li>Buat kata sandi yang <strong>kuat dan unik</strong>.</li>
              </ul>
            </li>

            <li>
              Perhatikan hal penting berikut saat mengisi formulir:
              <ul class="mt-2 ms-6 list-disc space-y-1 text-slate-700">
                <li>Kolom bertanda <span class="text-rose-600 font-bold">*</span> wajib diisi.</li>
                <li>
                  Usahakan memenuhi unsur <strong>4W + 1H</strong>:
                  <em>What, Where, When, Who, How</em>.
                </li>
                <li>
                  Jika ada bukti (foto/dokumen), lampirkan di halaman pengaduan.
                </li>
              </ul>
            </li>

            <li>
              Setelah mengirim, laporan Anda akan <strong>ditindaklanjuti</strong>.
              Cek status di menu <em>Riwayat Laporan</em> pada akun Anda.
            </li>

            <li>
              Data & identitas Anda bersifat
              <span class="font-semibold text-emerald-700">rahasia</span>. Kami
              berkomitmen pada keamanan & pemulihan penyintas.
            </li>
          </ol>

          {{-- Catatan --}}
          <div class="mt-6 rounded-xl border border-purple-200 bg-purple-50 p-4 text-sm text-slate-700">
            <div class="font-semibold text-purple-800">Tips:</div>
            <p class="mt-1">
              Tulis kronologi secara singkat, jelas, dan kronologis. Hindari menyebutkan info
              yang berpotensi membahayakan keselamatan Anda di ruang publik.
            </p>
          </div>

          {{-- CTA --}}
          <div class="mt-8 flex flex-wrap items-center gap-3">
            <a href="{{ url('/reports/create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-purple-700 px-5 py-2.5
                      font-semibold text-white hover:bg-purple-800 focus:outline-none focus:ring-2
                      focus:ring-purple-400/60">
              Buka Form Pengaduan
            </a>

            <a href="{{ url('/reports') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300
                      bg-white px-5 py-2.5 font-semibold text-slate-800 hover:bg-slate-50
                      focus:outline-none focus:ring-2 focus:ring-purple-300">
              Lihat Riwayat Laporan
            </a>
          </div>
        </div>
      </article>
    </div>
  </div>
</x-app-layout>
