<x-app-layout>
   {{-- top-menu --}}
   <x-slot name="header">
    @include('profile.partials.top-menu')
    </x-slot>
  {{-- Main content --}}
  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <div class="grid grid-cols-1 lg:grid-cols-1 ">
        {{-- Poster kiri (gambar statis / banner) --}}
        <x-ui.card class="flex items-center justify-center h-[260px] sm:h-[300px]">
          <div class="text-center">
            <div class="text-5xl font-black tracking-tight text-slate-900">Awasi</div>
            <div class="mt-1 text-3xl font-extrabold text-purple-700">dengan</div>
            <div class="mt-1 text-5xl font-black text-amber-600">Sistem Anda</div>
            <p class="mt-3 text-sm text-slate-600 max-w-xs mx-auto">
              Laporkan pelanggaran secara aman & rahasia.
            </p>
          </div>
        </x-ui.card>   
      </div>

      {{-- Row: Stats --}}
      <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        <x-ui.stat title="Laporan Baru" value="7"/>
        <x-ui.stat title="Kasus Aktif" value="18"/>
        <x-ui.stat title="Ditindaklanjuti" value="12"/>
        <x-ui.stat title="Selesai Bulan Ini" value="5"/>
      </div>

      {{-- Row: Quick actions + Info --}}
      <div id="pengaduan" class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-ui.card>
          <x-slot:header>Pengaduan Cepat</x-slot:header>
          <div class="grid sm:grid-cols-2 gap-3">
            <a href="{{ url('/reports/create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-purple-700 px-4 py-2.5 font-semibold text-white hover:bg-purple-800">
              Buat Laporan
            </a>
            <a href="{{ url('/reports') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 font-semibold text-slate-800 hover:bg-slate-100">
              Riwayat Laporan
            </a>
          </div>
          <p class="mt-3 text-sm text-slate-600">
            Laporan Anda bersifat <strong>rahasia</strong>. Mohon sertakan kronologi singkat & bukti pendukung.
          </p>
        </x-ui.card>

        <x-ui.card>
          <x-slot:header>Panduan Singkat</x-slot:header>
          <ol class="list-decimal list-inside text-sm text-slate-700 space-y-1">
            <li>Masuk/daftar akun.</li>
            <li>Isi formulir pengaduan dengan lengkap.</li>
            <li>Pantau status & tanggapan petugas.</li>
            <li>Jaga kerahasiaan kredensial Anda.</li>
          </ol>
          <a href="#panduan" class="mt-3 inline-flex text-sm font-semibold text-purple-700 hover:text-purple-800">Lihat panduan lengkap →</a>
        </x-ui.card>

        <x-ui.card>
          <x-slot:header>Kontak Layanan</x-slot:header>
          <ul class="text-sm text-slate-700 space-y-1">
            <li>WhatsApp: <a class="underline hover:text-purple-700" href="https://wa.me/6282306738686">0823-0673-8686</a></li>
            <li>Email: <a class="underline hover:text-purple-700" href="mailto:cp.wccbengkulu@gmail.com">cp.wccbengkulu@gmail.com</a></li>
            <li>Alamat: Jl. Indragiri 1 No. 03, Kota Bengkulu</li>
          </ul>
        </x-ui.card>
      </div>

      {{-- Footer --}}
      <div class="mt-8 text-center text-xs text-slate-500">
        © {{ date('Y') }} {{ config('app.name') }} — Berperspektif korban • Rahasia • Aman
      </div>
    </div>
  </div>
</x-app-layout>
