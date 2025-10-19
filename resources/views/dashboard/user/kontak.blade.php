{{-- resources/views/dashboard/user/hubungi-kami.blade.php --}}
<x-app-layout>
  {{-- Top-menu pada slot header --}}
    <x-slot name="header">
    @include('profile.partials.top-menu')
    </x-slot>
 

  <div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

      

      {{-- Grid: Map (L) + Info (R) --}}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- MAP --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            {{-- Title bar --}}
        <div class="mb-4">
            <h1 class="text-2xl font-extrabold text-slate-900">Hubungi Kami</h1>
        </div>
          <h2 class="mb-3 text-sm font-semibold text-slate-700">
            Lokasi Kantor
          </h2>

          {{-- 16:9 responsive map --}}
          <div class="relative w-full overflow-hidden rounded-xl" style="padding-bottom:56.25%;">
            <iframe
              class="absolute inset-0 h-full w-full"
              src="https://www.google.com/maps?q=-3.8178125,102.2836875&z=15&output=embed"
              style="border:0;"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              aria-label="Lokasi kantor">
            </iframe>
          </div>

          <div class="mt-3 flex flex-wrap gap-3">
            <a href="https://maps.google.com/?q=-3.8178125,102.2836875"
               target="_blank" rel="noopener"
               class="inline-flex items-center rounded-lg bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800">
              Lihat di Google Maps
            </a>
            <a href="https://www.google.com/maps/dir/?api=1&destination=-3.8178125,102.2836875&travelmode=driving"
               target="_blank" rel="noopener"
               class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-100">
              Dapatkan Rute
            </a>
          </div>
        </div>

        {{-- INFO CARD --}}
        <div class="relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">          
          <h2 class="text-lg font-extrabold text-slate-900">
            Pengaduan bisa Anda sampaikan melalui:
          </h2>

          <div class="mt-4 space-y-4 text-slate-700">
            <div>
              <div class="border-b border-slate-200 pb-2 text-sm font-semibold text-slate-600">
                Layanan pesan singkat (SMS/WhatsApp)
              </div>
              <p class="mt-2 text-base">
                Kirim ke <span class="font-semibold text-purple-700">0823-0673-8686</span>
                dengan format:
              </p>
              <ul class="mt-2 list-disc list-inside space-y-1 text-base">
                <li><span class="font-semibold">nama</span></li>
                <li><span class="font-semibold">pelapor#nama</span></li>
                <li><span class="font-semibold">terlapor#isi pengaduan</span></li>
              </ul>
            </div>

            <div>
              <div class="border-b border-slate-200 pb-2 text-sm font-semibold text-slate-600">
                Surat Elektronik (Email)
              </div>
              <p class="mt-2 text-base">
                <a href="mailto:cp.wccbengkulu@gmail.com" class="font-semibold text-purple-700 underline">
                  cp.wccbengkulu@gmail.com
                </a>
              </p>
            </div>

            <div>
              <div class="border-b border-slate-200 pb-2 text-sm font-semibold text-slate-600">
                Telepon/WhatsApp
              </div>
              <p class="mt-2 text-base">
                <a href="https://wa.me/6282306738686" class="font-semibold text-purple-700 underline">
                  0823-0673-8686
                </a>
              </p>
            </div>

            <div>
              <div class="border-b border-slate-200 pb-2 text-sm font-semibold text-slate-600">
                Alamat Kantor
              </div>
              <address class="mt-2 not-italic text-base">
                Jl. Indragiri 1 No. 03, Kel. Padang Harapan<br/>
                Kota Bengkulu
              </address>
            </div>

            <div class="rounded-xl bg-gradient-to-r from-purple-50 to-amber-50 p-4 text-sm text-slate-700">
              <span class="font-semibold text-purple-800">Catatan:</span>
              Demi keamanan, hindari menyertakan informasi yang dapat membahayakan keselamatan Anda di ruang publik.
            </div>
          </div>
        </div>
      </div>

      {{-- Footer kecil --}}
      <p class="mt-8 text-center text-xs text-slate-500">
        © {{ date('Y') }} {{ config('app.name') }} — Berperspektif korban • Rahasia • Aman
      </p>
    </div>
  </div>
</x-app-layout>
