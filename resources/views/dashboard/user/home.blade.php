<x-app-layout>
  {{-- top-menu --}}
  <x-slot name="header">
    @include('profile.partials.top-menu')
  </x-slot>

  @php
    // Hitung ringkasan pengaduan untuk user yang login
    $meId = auth()->id();

    $counts = [
      'baru'   => \App\Models\Complaint::where('user_id', $meId)->where('status', 'submitted')->count(),
      'aktif'  => \App\Models\Complaint::where('user_id', $meId)->whereIn('status', ['submitted','in_review','follow_up'])->count(),
      'follow_up' => \App\Models\Complaint::where('user_id', $meId)->where('status', 'follow_up')->count(),
      'selesai_bulan_ini' => \App\Models\Complaint::where('user_id', $meId)
                            ->where('status', 'closed')
                            ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
                            ->count(),
    ];

    $last = \App\Models\Complaint::with('user')->where('user_id', $meId)->latest()->first();

    $statusLabels = [
      'submitted'  => 'Diajukan',
      'in_review'  => 'Ditinjau',
      'follow_up'  => 'Ditindaklanjuti',
      'closed'     => 'Selesai',
    ];
    $statusClasses = [
      'submitted'  => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
      'in_review'  => 'bg-blue-100 text-blue-800 ring-blue-200',
      'follow_up'  => 'bg-amber-100 text-amber-800 ring-amber-200',
      'closed'     => 'bg-green-100 text-green-800 ring-green-200',
    ];
    $lastBadge = $last ? ($statusClasses[$last->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200') : '';
  @endphp

  {{-- Main content --}}
  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      {{-- HERO / Poster dengan konten dinamis --}}
      <div class="grid grid-cols-1 lg:grid-cols-1">
        <x-ui.card class="relative overflow-hidden">
          {{-- dekorasi latar --}}
          <div class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute right-[-15%] top-[-30%] h-72 w-72 rounded-full bg-purple-200/60 blur-3xl"></div>
            <div class="absolute left-[-10%] bottom-[-30%] h-72 w-72 rounded-full bg-amber-200/60 blur-3xl"></div>
          </div>

          <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            {{-- Kiri: salam + CTA + chips ringkasan --}}
            <div>
              <p class="text-sm text-slate-600">Halo, <span class="font-semibold text-slate-800">{{ auth()->user()->name }}</span> 👋</p>
              <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900">
                Laporkan dengan aman &amp; rahasia
              </h1>
              <p class="mt-2 max-w-xl text-sm text-slate-600">
                Sampaikan kronologi singkat & bukti pendukung. Pantau perkembangan kasus Anda di dashboard ini.
              </p>

              <div class="mt-4 flex flex-wrap items-center gap-2">
                <a href="{{ route('complaints.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-purple-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-purple-800">
                  Buat Laporan
                </a>
                <a href="{{ route('complaints.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-100">
                  Riwayat Laporan
                </a>
              </div>

              {{-- chips ringkasan cepat --}}
              <div class="mt-4 flex flex-wrap gap-2">
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                  Baru: {{ $counts['baru'] }}
                </span>
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                  Aktif: {{ $counts['aktif'] }}
                </span>
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                  Ditindaklanjuti: {{ $counts['follow_up'] }}
                </span>
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                  Selesai bln ini: {{ $counts['selesai_bulan_ini'] }}
                </span>
              </div>
            </div>

            {{-- Kanan: pengaduan terakhir --}}
            <div class="w-full sm:w-auto">
              <div class="rounded-xl border border-slate-200 bg-white/70 p-4 shadow-sm backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pengaduan Terakhir</p>
                @if($last)
                  <div class="mt-2 text-sm">
                    <div class="flex items-center justify-between gap-3">
                      <div class="font-medium text-slate-800">#{{ $last->code ?? $last->id }}</div>
                      <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium ring-1 {{ $lastBadge }}">
                        {{ $statusLabels[$last->status] ?? ucfirst(str_replace('_',' ',$last->status)) }}
                      </span>
                    </div>
                    <div class="mt-1 line-clamp-2 text-slate-600">{{ \Illuminate\Support\Str::limit($last->title, 60) }}</div>
                    <div class="mt-1 text-[11px] text-slate-500">
                      {{ optional($last->updated_at)->diffForHumans() }}
                    </div>
                    <div class="mt-3">
                      <a href="{{ route('complaints.show', $last) }}"
                         class="inline-flex items-center rounded-lg border px-3 py-1.5 text-xs font-semibold hover:bg-slate-50">
                        Lihat Detail
                      </a>
                    </div>
                  </div>
                @else
                  <p class="mt-2 text-sm text-slate-600">Belum ada pengaduan. Mulai dengan tombol <span class="font-semibold">Buat Laporan</span>.</p>
                @endif
              </div>
            </div>
          </div>
        </x-ui.card>
      </div>

      {{-- Row: Stats (otomatis dari data user) --}}
      <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        <x-ui.stat title="Laporan Baru" value="{{ $counts['baru'] }}"/>
        <x-ui.stat title="Kasus Aktif" value="{{ $counts['aktif'] }}"/>
        <x-ui.stat title="Ditindaklanjuti" value="{{ $counts['follow_up'] }}"/>
        <x-ui.stat title="Selesai Bulan Ini" value="{{ $counts['selesai_bulan_ini'] }}"/>
      </div>

      {{-- Row: Quick actions + Info --}}
      <div id="pengaduan" class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-ui.card>
          <x-slot:header>Pengaduan Cepat</x-slot:header>
          <div class="grid sm:grid-cols-2 gap-3">
            <a href="{{ route('complaints.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-purple-700 px-4 py-2.5 font-semibold text-white hover:bg-purple-800">
              Buat Laporan
            </a>
            <a href="{{ route('complaints.index') }}"
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
            <li>Pantau status &amp; tanggapan petugas.</li>
            <li>Jaga kerahasiaan kredensial Anda.</li>
          </ol>
          <a href="{{ route('cara-melapor') }}" class="mt-3 inline-flex text-sm font-semibold text-purple-700 hover:text-purple-800">Lihat panduan lengkap →</a>
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

    </div>
  </div>
</x-app-layout>
