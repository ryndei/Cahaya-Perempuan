<x-app-layout>
  <x-slot name="header">
    @include('profile.partials.top-menu-admin')
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      {{-- Filter --}}
      <form method="GET" class="mb-5 flex flex-wrap items-end gap-3">
        <div>
          <label class="block text-xs text-slate-600">Dari</label>
          <input type="date" name="from" value="{{ request('from') }}" class="border rounded-lg px-3 py-2 text-sm bg-white">
        </div>
        <div>
          <label class="block text-xs text-slate-600">Sampai</label>
          <input type="date" name="to" value="{{ request('to') }}" class="border rounded-lg px-3 py-2 text-sm bg-white">
        </div>
        <button class="rounded-lg bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 text-sm transition">Terapkan</button>
      </form>

      {{-- KPI --}}
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <x-ui.stat title="Laporan Baru"        value="{{ $kpis['baru'] ?? 0 }}"/>
        <x-ui.stat title="Kasus Aktif"         value="{{ $kpis['aktif'] ?? 0 }}"/>
        <x-ui.stat title="Ditindaklanjuti"     value="{{ $kpis['follow_up'] ?? 0 }}"/>
        <x-ui.stat title="Selesai Bulan Ini"   value="{{ $kpis['selesai_bulan_ini'] ?? 0 }}"/>
        <x-ui.stat title="Rata2 Selesai (jam)" value="{{ $kpis['avg_resolve_hours'] ?? 0 }}"/>
      </div>

      {{-- Charts row 1 --}}
      <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-ui.card class="h-full">
          <x-slot name="header">Tren 30 Hari</x-slot>
          <div class="relative h-48 md:h-56"><canvas id="chartTrend" class="absolute inset-0"></canvas></div>
        </x-ui.card>
          <x-ui.card class="h-full">
  <x-slot:header>Distribusi Status</x-slot:header>

  {{-- Tinggikan box & paksa canvas isi penuh --}}
  <div class="relative h-64 md:h-80">
    <canvas id="chartStatus" class="absolute inset-0 w-full h-full"></canvas>
  </div>
</x-ui.card>

        <x-ui.card class="h-full">
          <x-slot name="header">Top Kategori</x-slot>
          <div class="relative h-48 md:h-56"><canvas id="chartCategory" class="absolute inset-0"></canvas></div>
        </x-ui.card>
      </div>

      {{-- Charts row 2: Umur (Bucket default) --}}
      <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        <x-ui.card>
          <x-slot name="header">
            <div class="flex items-center justify-between">
              <span>Umur Pelapor (Bucket)</span>
              <button type="button" id="btnToggleRepAge" class="text-xs px-3 py-1 rounded border hover:bg-slate-50">
                Lihat detail umur
              </button>
            </div>
          </x-slot>
          <div class="space-y-3">
            <div class="relative h-52"><canvas id="chartReporterAgesBucket" class="absolute inset-0"></canvas></div>
            <div id="wrapRepExact" class="hidden">
              <hr class="my-2">
              <div class="text-xs text-slate-500 mb-1">Detail umur (exact)</div>
              <div class="relative h-52"><canvas id="chartReporterAgesExact" class="absolute inset-0"></canvas></div>
            </div>
          </div>
        </x-ui.card>

        <x-ui.card>
          <x-slot name="header">
            <div class="flex items-center justify-between">
              <span>Umur Pelaku (Bucket)</span>
              <button type="button" id="btnTogglePerpAge" class="text-xs px-3 py-1 rounded border hover:bg-slate-50">
                Lihat detail umur
              </button>
            </div>
          </x-slot>
          <div class="space-y-3">
            <div class="relative h-52"><canvas id="chartPerpAgesBucket" class="absolute inset-0"></canvas></div>
            <div id="wrapPerpExact" class="hidden">
              <hr class="my-2">
              <div class="text-xs text-slate-500 mb-1">Detail umur (exact)</div>
              <div class="relative h-52"><canvas id="chartPerpAgesExact" class="absolute inset-0"></canvas></div>
            </div>
          </div>
        </x-ui.card>
      </div>

      {{-- Charts row 3: Top Pekerjaan --}}
      <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-ui.card>
          <x-slot name="header">Top Pekerjaan Pelapor</x-slot>
          <div class="relative h-56"><canvas id="chartJobsReporter" class="absolute inset-0"></canvas></div>
        </x-ui.card>

        <x-ui.card>
          <x-slot name="header">Top Pekerjaan Pelaku</x-slot>
          <div class="relative h-56"><canvas id="chartJobsPerp" class="absolute inset-0"></canvas></div>
        </x-ui.card>
      </div>

      {{-- …(tabel pengaduan terbaru kamu biarkan seperti sebelumnya)… --}}
      @include('profile.partials.recent-table', [
  'recent'            => $recent,
  'statusLabels'      => $statusLabels,
  'statusClasses'     => $statusClasses,
  'shortStatusLabels' => $shortStatusLabels,
])

    </div>
  </div>

  {{-- Hydrate untuk JS --}}
  <script type="application/json" id="admin-dashboard-data">
    {!! json_encode([
      'timeseries'         => $timeseries        ?? ['labels'=>[], 'counts'=>[]],
      'byStatus'           => $byStatus          ?? [],
      'byCategory'         => $byCategory        ?? [],
      'byProvince'         => $byProvince        ?? [],
      'ageBucketsReporter' => $ageBucketsReporter ?? [],
      'ageBucketsPerp'     => $ageBucketsPerp     ?? [],
      'agesReporterExact'  => $agesReporterExact  ?? ['labels'=>[], 'counts'=>[]],
      'agesPerpExact'      => $agesPerpExact      ?? ['labels'=>[], 'counts'=>[]],
      'jobsReporter'       => $jobsReporter       ?? [],
      'jobsPerp'           => $jobsPerp           ?? [],
    ], JSON_UNESCAPED_UNICODE) !!}
  </script>

  @vite('resources/js/pages/admin-dashboard.js')
</x-app-layout>
