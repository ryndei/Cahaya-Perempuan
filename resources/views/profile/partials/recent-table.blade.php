{{-- resources/views/dashboard/admin/partials/_recent-table.blade.php --}}

{{-- Tabel: Pengaduan Terbaru (DESKTOP ≥ md) --}}
<div class="mt-8 hidden md:block">
  <div class="rounded-2xl border border-slate-200 bg-white">
    <div class="flex items-center justify-between px-4 py-3">
      <h2 class="text-sm font-semibold text-slate-700">Pengaduan Terbaru</h2>
      <a href="{{ route('admin.complaints.index') }}" class="text-sm text-purple-700 hover:underline">Lihat semua</a>
    </div>

    <table class="w-full table-fixed text-sm">
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
            $badgeClass  = $statusClasses[$c->status] ?? 'bg-slate-100 text-slate-700';
            $fullStatus  = $statusLabels[$c->status] ?? \Illuminate\Support\Str::headline($c->status);
            $shortStatus = $shortStatusLabels[$c->status] ?? $fullStatus;
          @endphp
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3 align-top">{{ $loop->iteration }}</td>

            <td class="px-4 py-3 align-top font-medium truncate">
              {{ $c->code ?? $c->id }}
            </td>

            <td class="px-4 py-3 align-top">
              <div class="font-medium text-slate-900 truncate">
                {{ \Illuminate\Support\Str::limit($c->category ?? 'Tanpa kategori', 80) }}
              </div>
              <div class="text-slate-500 truncate">
                {{ \Illuminate\Support\Str::limit($c->description, 120) }}
              </div>
            </td>

            <td class="px-4 py-3 align-top">
              <div class="font-medium truncate">{{ optional($c->user)->name ?? '—' }}</div>
              <div class="text-slate-500 text-xs truncate">{{ optional($c->user)->email ?? '' }}</div>
            </td>

            <td class="px-4 py-3 align-top">
              <span
                class="inline-block rounded-full px-3 py-1 text-xs font-medium leading-5 {{ $badgeClass }} max-w-full [overflow-wrap:anywhere] text-center"
                title="{{ $fullStatus }}">
                {{ $shortStatus }}
              </span>
            </td>

            <td class="px-4 py-3 align-top">
              <div class="truncate">{{ $c->created_at?->locale('id')->translatedFormat('d M Y') }}</div>
              <div class="text-xs text-slate-500 truncate">{{ optional($c->created_at)->diffForHumans() }}</div>
            </td>

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
      @php
        $badgeClass  = $statusClasses[$c->status] ?? 'bg-slate-100 text-slate-700';
        $fullStatus  = $statusLabels[$c->status] ?? \Illuminate\Support\Str::headline($c->status);
        $shortStatus = $shortStatusLabels[$c->status] ?? $fullStatus;
      @endphp
      <div class="rounded-xl border border-slate-200 bg-white p-4">
        <div class="grid grid-cols-[1fr_auto] items-start gap-3">
          <div class="min-w-0">
            <div class="text-xs text-slate-500">
              #{{ $c->code ?? $c->id }}
              <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium leading-5 {{ $badgeClass }}"
                    title="{{ $fullStatus }}">
                {{ $shortStatus }}
              </span>
            </div>

            <div class="mt-0.5 text-sm font-semibold text-slate-900 break-words">
              {{ \Illuminate\Support\Str::limit($c->category ?? 'Tanpa kategori', 80) }}
            </div>

            <div class="mt-1 text-xs text-slate-500">
              Pelapor:
              <span class="font-medium text-slate-700">{{ optional($c->user)->name ?? '—' }}</span>
              <span class="block">{{ optional($c->user)->email }}</span>
            </div>

            <div class="mt-1 font-bold text-slate-700">Deskripsi</div>
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
