<x-app-layout>
  {{-- top-menu --}}
  <x-slot name="header">
    @include('profile.partials.top-menu-admin')
  </x-slot>

  @php
    $statusLabels = \App\Models\Complaint::statusLabels();

    // badge status utk tabel kecil & kartu mobile
    $statusClasses = [
      'submitted'        => 'bg-slate-100 text-slate-700',
      'in_review'        => 'bg-amber-100 text-amber-800',
      'follow_up'        => 'bg-blue-100 text-blue-800',
      'closed'           => 'bg-emerald-100 text-emerald-800',
      'closed_pa'        => 'bg-emerald-100 text-emerald-800',
      'closed_pn'        => 'bg-teal-100 text-teal-800',
      'closed_mediation' => 'bg-lime-100 text-lime-800',
    ];

    // Label singkat (tooltip tetap pakai full label)
    $shortStatusLabels = [
      'submitted'        => 'Diajukan',
      'in_review'        => 'Ditinjau',
      'follow_up'        => 'Ditindaklanjuti',
      'closed'           => 'Selesai',
      'closed_pa'        => 'Selesai — PA',
      'closed_pn'        => 'Selesai — PN',
      'closed_mediation' => 'Selesai — Mediasi',
    ];

  @endphp
  {{-- Main content --}}
  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      {{-- Banner/kartu besar -> Grafik Tren 30 Hari --}}
      <div class="grid grid-cols-1 lg:grid-cols-1">
        <x-ui.card class="relative h-[260px] sm:h-[300px] p-4">
          <div class="flex items-center justify-between">
            <h1 class="text-base sm:text-lg font-semibold tracking-wide">Tren Pengaduan 30 Hari Terakhir</h1>
            <div class="text-xs text-slate-500">
              Total: {{ isset($trendCounts) ? array_sum($trendCounts) : 0 }}
            </div>
          </div>

          <div class="mt-2 h-[200px] sm:h-[240px]">
            <canvas id="complaintTrendChart" class="w-full h-full"></canvas>
          </div>

          {{-- fallback bila tidak ada data --}}
          @if(empty($trendCounts) || collect($trendCounts)->sum() === 0)
            <div class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm">
              Belum ada data pengaduan untuk periode ini.
            </div>
          @endif
        </x-ui.card>
      </div>

      {{-- Row: Stats --}}
      <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.complaints.index', ['status' => 'submitted']) }}" class="block">
          <x-ui.stat title="Laporan Baru" value="{{ $counts['baru'] ?? 0 }}" />
        </a>

        <a href="{{ route('admin.complaints.index', ['status_group' => 'active']) }}" class="block">
          <x-ui.stat title="Kasus Aktif" value="{{ $counts['aktif'] ?? 0 }}" />
        </a>

        <a href="{{ route('admin.complaints.index', ['status' => 'follow_up']) }}" class="block">
          <x-ui.stat title="Ditindaklanjuti" value="{{ $counts['follow_up'] ?? 0 }}" />
        </a>

        <a href="{{ route('admin.complaints.index', [
              'status_group' => 'closed_all',
              'from'         => now()->startOfMonth()->toDateString(),
              'to'           => now()->endOfMonth()->toDateString(),
            ]) }}" class="block">
          <x-ui.stat title="Selesai Bulan Ini" value="{{ $counts['selesai_bulan_ini'] ?? 0 }}" />
        </a>
      </div>

      {{-- Tabel: Pengaduan Terbaru (DESKTOP ≥ md) --}}
      <div class="mt-6 hidden md:block">
        <div class="rounded-2xl border border-slate-200 bg-white">
          <div class="flex items-center justify-between px-4 py-3">
            <h2 class="text-sm font-semibold text-slate-700">Pengaduan Terbaru</h2>
            <a href="{{ route('admin.complaints.index') }}" class="text-sm text-purple-700 hover:underline">Lihat semua</a>
          </div>

          <table class="w-full table-fixed text-sm">
            {{-- ⬇️ Kunci lebar kolom dengan persentase agar TIDAK ada horizontal scroll --}}
            <colgroup>
              <col class="w-[5%]">
              <col class="w-[12%]">
              <col class="w-[35%]">
              <col class="w-[15%]">
              <col class="w-[14%]">
              <col class="w-[12%]">
              <col class="w-[7%]">
            </colgroup>

            <thead class="bg-slate-50 text-slate-700">
              <tr>
                <th class="px-4 py-3 text-left font-medium">No</th>
                <th class="px-4 py-3 text-left font-medium">Kode</th>
                <th class="px-4 py-3 text-left font-medium">Deskripsi</th>
                <th class="px-4 py-3 text-left font-medium">Pelapor</th>
                <th class="px-4 py-3 text-left font-medium">Status</th>
                <th class="px-4 py-3 text-left font-medium">Dibuat</th>
                <th class="px-4 py-3"></th>
              </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
              @forelse($recent as $c)
                @php
                  $badge = $statusClasses[$c->status] ?? 'bg-slate-100 text-slate-700';
                  $statusText = $statusLabels[$c->status] ?? ucfirst(str_replace('_',' ',$c->status));
                  $fullStatus  = $statusLabels[$c->status] ?? \Illuminate\Support\Str::headline($c->status);
                  $shortStatus = $shortStatusLabels[$c->status] ?? $fullStatus;
                @endphp
                <tr class="hover:bg-slate-50">
                  {{-- No --}}
                  <td class="px-4 py-3 align-top">
                    {{ $loop->iteration }}
                  </td>

                  {{-- Kode --}}
                  <td class="px-4 py-3 align-top font-medium truncate">
                    {{ $c->code ?? $c->id }}
                  </td>

                  {{-- Deskripsi (judul + potongan) --}}
                  <td class="px-4 py-3 align-top">
                    <div class="font-medium text-slate-900 truncate">
                      {{ \Illuminate\Support\Str::limit($c->title, 80) }}
                    </div>
                    <div class="text-slate-500 truncate">
                      {{ \Illuminate\Support\Str::limit($c->description, 120) }}
                    </div>
                  </td>

                  {{-- Pelapor --}}
                  <td class="px-4 py-3 align-top">
                    <div class="font-medium truncate">{{ optional($c->user)->name ?? '—' }}</div>
                    <div class="text-slate-500 text-xs truncate">{{ optional($c->user)->email ?? '' }}</div>
                  </td>

                  {{-- Status (badge wrap supaya tidak melebar) --}}
                  <td class="px-4 py-3 align-top">
                    <span
                      class="inline-block rounded-full px-3 py-1 text-xs font-medium leading-5 {{ $badge }} max-w-full [overflow-wrap:anywhere] text-center"
                      title="{{ $fullStatus }}">
                      {{ $shortStatus }}
                    </span>
                  </td>

                  {{-- Dibuat --}}
                  <td class="px-4 py-3 align-top">
                    <div class="truncate">{{ $c->created_at?->locale('id')->translatedFormat('d M Y') }}</div>
                    <div class="text-xs text-slate-500 truncate">{{ optional($c->created_at)->diffForHumans() }}</div>
                  </td>

                  {{-- Aksi --}}
                  <td class="px-4 py-3 align-top text-right">
                    <a href="{{ route('admin.complaints.show', $c) }}"
                       class="rounded-md border px-3 py-1.5 text-xs hover:bg-slate-50">
                      Lihat
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="px-4 py-6 text-center text-slate-500">Belum ada pengaduan.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- Kartu: Pengaduan Terbaru (MOBILE < md) --}}
      <div class="mt-6 md:hidden">
        <div class="mb-3 flex items-center justify-between">
          <h2 class="text-sm font-semibold text-slate-700">Pengaduan Terbaru</h2>
          <a href="{{ route('admin.complaints.index') }}" class="text-sm text-purple-700 hover:underline">Lihat semua</a>
        </div>

        <div class="space-y-3">
          @forelse ($recent as $c)
            @php $badge = $statusClasses[$c->status] ?? 'bg-slate-100 text-slate-700'; @endphp
            <div class="rounded-xl border border-slate-200 bg-white p-4">
              <div class="grid grid-cols-[1fr_auto] items-start gap-3">
                <div class="min-w-0">
                  <div class="text-xs text-slate-500">#{{ $c->code ?? $c->id }} 
                    <span
                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium leading-5 {{ $badge }}"
                    title="{{ $fullStatus }}">
                    {{ $shortStatus }}
                  </span>
                </div>
                  <div class="mt-0.5 text-sm font-semibold text-slate-900 break-words">
                    {{ \Illuminate\Support\Str::limit($c->title, 80) }}
                  </div>
                                  
                  <div class="mt-1 text-xs text-slate-500">
                    Pelapor:
                    <span class="font-medium text-slate-700">{{ optional($c->user)->name ?? '—' }}</span>
                    <span class="block">{{ optional($c->user)->email }}</span>
                  </div>
                  <div class="mt-1 font-bold text-slate-700">
                    Deskripsi
                  </div>
                 
                  <div class="mt-1 text-sm text-slate-600 break-words"> 
                    {{ \Illuminate\Support\Str::limit($c->description, 120) }}
                  </div>
                </div>
                
                <a href="{{ route('admin.complaints.show', $c) }}"
                   class="shrink-0 rounded-md border px-3 py-1.5 text-xs hover:bg-slate-50">Lihat</a>
              </div>
            </div>
          @empty
            <div class="rounded-xl border border-slate-200 bg-white p-6 text-center text-slate-500">
              Belum ada pengaduan.
            </div>
          @endforelse
        </div>
      </div>

    </div>
  </div>

  {{-- Chart.js CDN + init --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script>
    (function () {
      const el = document.getElementById('complaintTrendChart');
      if (!el) return;

      const labels = @json($trendLabels ?? []);
      const data = @json($trendCounts ?? []);

      if (!labels.length) return;

      new Chart(el, {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: 'Pengaduan',
            data,
            tension: 0.35,
            fill: true,
            borderWidth: 2,
            pointRadius: 0,
            borderColor: 'rgba(99, 102, 241, 1)',
            backgroundColor: (ctx) => {
              const chart = ctx.chart;
              const {ctx: c, chartArea} = chart;
              if (!chartArea) return null;
              const g = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
              g.addColorStop(0, 'rgba(99, 102, 241, 0.25)');
              g.addColorStop(1, 'rgba(99, 102, 241, 0.02)');
              return g;
            },
          }]
        },
        options: {
          maintainAspectRatio: false,
          interaction: { mode: 'index', intersect: false },
          plugins: {
            legend: { display: false },
            tooltip: { mode: 'index', intersect: false }
          },
          scales: {
            x: {
              grid: { display: false },
              ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8 }
            },
            y: {
              beginAtZero: true,
              ticks: { precision: 0 },
              grid: { color: 'rgba(148,163,184,0.2)' }
            }
          }
        }
      });
    })();
  </script>
</x-app-layout>
