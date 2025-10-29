{{-- resources/views/admin/complaints/index.blade.php --}}
<x-app-layout>
  {{-- Header/topbar admin --}}
  <x-slot name="header">
    @include('profile.partials.top-menu-admin')
  </x-slot>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Title + Export --}}
    <div class="mb-6 flex items-start justify-between gap-3">
      <div>
        <h1 class="text-2xl font-semibold">Manajemen Pengaduan</h1>
        <p class="text-sm text-slate-600">Pantau semua laporan yang masuk, gunakan filter untuk mempercepat penelusuran.</p>
      </div>

      {{-- Export Excel --}}
      <a
        href="{{ route('admin.complaints.export.xlsx', request()->query()) }}"
        class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-2 text-white font-semibold hover:bg-emerald-700"
        title="Export data sesuai filter aktif ke Excel"
      >
        Export Excel
      </a>
    </div>

    @if (session('status'))
      <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        {{ session('status') }}
      </div>
    @endif
    @if (session('success'))
      <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        {{ session('success') }}
      </div>
    @endif

    {{-- Filter & Search --}}
    <form method="GET" action="{{ route('admin.complaints.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 mb-6">
      <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Cari</label>
        <input type="text" name="q" value="{{ request('q') }}"
               class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
               placeholder="deskripsi, nama/email pelapor">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Status</label>
        <select name="status" class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
          <option value="">Semua</option>
          @foreach ($statusLabels as $val => $label)
            <option value="{{ $val }}" @selected(request('status')===$val)>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Dari tanggal</label>
        <input type="date" name="from" value="{{ request('from') }}"
               class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Sampai</label>
        <input type="date" name="to" value="{{ request('to') }}"
               class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Per halaman</label>
        <select name="per_page" class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
          @foreach ([10,20,50,100] as $n)
            <option value="{{ $n }}" @selected((int)request('per_page',10)===$n)>{{ $n }}</option>
          @endforeach
        </select>
      </div>

      <div class="md:col-span-6 flex gap-3">
        <button class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Terapkan</button>
        <a href="{{ route('admin.complaints.index') }}"
           class="inline-flex items-center rounded-md border px-4 py-2 hover:bg-slate-50">Reset</a>
      </div>
    </form>

    {{-- DESKTOP TABLE (>= md) --}}
    <div class="hidden md:block">
      <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full table-fixed text-sm">
            <thead class="sticky top-0 z-10 bg-slate-50/95 backdrop-blur border-b border-slate-200">
              <tr class="text-slate-600">
                <th class="w-14 px-4 py-3 text-left text-xs font-semibold tracking-wide">No</th>
                <th class="w-[32rem] px-4 py-3 text-left text-xs font-semibold tracking-wide">Deskripsi</th>
                <th class="w-56 px-4 py-3 text-left text-xs font-semibold tracking-wide">Pelapor</th>
                <th class="w-44 px-4 py-3 text-left text-xs font-semibold tracking-wide">Status</th>
                <th class="w-64 px-4 py-3 text-left text-xs font-semibold tracking-wide">Aksi</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
            @forelse ($complaints as $c)
              @php
                $badgeClass  = $statusClasses[$c->status] ?? 'bg-slate-100 text-slate-700';
                $fullStatus  = $statusLabels[$c->status] ?? \Illuminate\Support\Str::headline($c->status);
                $shortStatus = $shortStatusLabels[$c->status] ?? $fullStatus;

                $changer    = optional($c->lastStatusActivity?->causer)->name;
                $changedAt  = optional($c->lastStatusActivity?->created_at);
                $changedTxt = $changedAt ? $changedAt->diffForHumans() : null;
                $tip        = trim(($changer ? "Terakhir diubah: {$changer}" : "Terakhir diubah") . ($changedTxt ? " • {$changedTxt}" : ""));
              @endphp

              <tr class="odd:bg-white even:bg-slate-50/50 hover:bg-indigo-50/40 transition-colors">
                <td class="px-4 py-3 align-top whitespace-nowrap">
                  {{ $loop->iteration + ($complaints->firstItem() - 1) }}
                </td>

                <td class="px-4 py-3 align-top">
                  <div class="font-medium text-slate-900">
                    #{{ $c->code ?? $c->id }} —
                    <span class="text-slate-700">{{ \Illuminate\Support\Str::limit($c->category ?? 'Tanpa kategori', 56) }}</span>
                  </div>
                  <div class="mt-0.5 text-slate-500 break-words max-w-[32rem]">
                    {{ \Illuminate\Support\Str::limit($c->description, 140) }}
                  </div>
                </td>

                <td class="px-4 py-3 align-top">
                  <div class="font-medium text-slate-900">{{ optional($c->user)->name ?? '—' }}</div>
                  <div class="text-slate-500 text-xs">{{ optional($c->user)->email ?? '' }}</div>
                </td>

                <td class="px-4 py-3 align-top">
                  <div class="whitespace-nowrap">
                    <span
                      class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium leading-5 {{ $badgeClass }}"
                      @if($tip) title="{{ $tip }}" @endif
                    >
                      {{ $shortStatus }}
                    </span>
                  </div>
                  @if($changer || $changedAt)
                    <div class="mt-1 text-[11px] text-slate-500 leading-4 max-w-[10.5rem] truncate">
                      @if($changer)Di Ubah Oleh <span class="font-medium text-slate-700">{{ $changer }}</span>@endif
                    </div>
                  @endif
                </td>

                <td class="px-4 py-3 align-top">
                  <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.complaints.show', $c) }}"
                       class="shrink-0 rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-700 hover:bg-slate-50">
                      Lihat
                    </a>

                    <form method="POST" action="{{ route('admin.complaints.updateStatus', $c) }}"
                          onsubmit="return confirm('Ubah status laporan ini?')"
                          class="flex flex-wrap items-center gap-2">
                      @csrf
                      @method('PATCH')

                      <select name="status"
                              class="w-44 rounded-md border-slate-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach ($statusLabels as $val => $label)
                          <option value="{{ $val }}" @selected($c->status===$val)>{{ $label }}</option>
                        @endforeach
                      </select>

                      <button
                        class="shrink-0 rounded-md bg-slate-800 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-slate-900">
                        Update
                      </button>

                      <input type="hidden" name="admin_note" value="">
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td class="px-4 py-10 text-center text-slate-500" colspan="5">Belum ada pengaduan.</td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- MOBILE CARDS (< md) --}}
    <div class="md:hidden space-y-3">
      @forelse ($complaints as $c)
        @php
          $badgeClass  = $statusClasses[$c->status] ?? 'bg-slate-100 text-slate-700';
          $fullStatus  = $statusLabels[$c->status] ?? \Illuminate\Support\Str::headline($c->status);
          $shortStatus = $shortStatusLabels[$c->status] ?? $fullStatus;

          $changer    = optional($c->lastStatusActivity?->causer)->name;
          $changedAt  = optional($c->lastStatusActivity?->created_at);
        @endphp

        <div class="rounded-xl border border-slate-200 bg-white p-4">
          <div class="grid grid-cols-[1fr_auto] items-start gap-3">
            <div class="min-w-0">
              <div class="text-sm font-semibold text-slate-900 break-words">
                #{{ $c->code ?? $c->id }} — {{ \Illuminate\Support\Str::limit($c->category ?? 'Tanpa kategori', 80) }}
              </div>
              <div class="mt-1 text-sm text-slate-600 break-words">
                {{ \Illuminate\Support\Str::limit($c->description, 120) }}
              </div>

              <div class="mt-2 space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                  <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium leading-5 {{ $badgeClass }}"
                        title="{{ $fullStatus }}">
                    {{ $shortStatus }}
                  </span>
                  <span class="text-xs text-slate-500">
                    • {{ optional($c->created_at)?->translatedFormat('d M Y H:i') }}
                    ({{ optional($c->created_at)?->diffForHumans() }})
                  </span>
                </div>

                @if($changer || $changedAt)
                  <div class="text-[11px] text-slate-500 leading-4">
                    @if($changer) oleh <span class="font-medium text-slate-700">{{ $changer }}</span>@endif
                    @if($changedAt) <span class="opacity-70">• {{ $changedAt->diffForHumans() }}</span> @endif
                  </div>
                @endif
              </div>

              <div class="mt-1 text-xs text-slate-500">
                Pelapor: <span class="font-medium text-slate-700">{{ optional($c->user)->name ?? '—' }}</span>
                <span class="hidden sm:inline">• {{ optional($c->user)->email }}</span>
              </div>
            </div>

            <a href="{{ route('admin.complaints.show', $c) }}"
               class="shrink-0 self-start rounded-md border px-3 py-1.5 text-xs hover:bg-slate-50">
              Lihat
            </a>
          </div>

          <form class="mt-3" method="POST" action="{{ route('admin.complaints.updateStatus', $c) }}"
                onsubmit="return confirm('Ubah status laporan ini?')">
            @csrf
            @method('PATCH')
            <div class="flex items-center gap-2">
              <select name="status"
                      class="flex-1 rounded-md border-slate-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                @foreach ($statusLabels as $val => $label)
                  <option value="{{ $val }}" @selected($c->status===$val)>{{ $label }}</option>
                @endforeach
              </select>
              <button class="rounded-md bg-slate-800 text-white text-xs px-3 py-1.5 hover:bg-slate-900">
                Update
              </button>
            </div>
            <input type="hidden" name="admin_note" value="">
          </form>
        </div>
      @empty
        <div class="rounded-xl border border-slate-200 bg-white p-6 text-center text-slate-500">
          Belum ada pengaduan.
        </div>
      @endforelse
    </div>

    <div class="mt-6">
      {{ $complaints->links() }}
    </div>
  </div>
</x-app-layout>
