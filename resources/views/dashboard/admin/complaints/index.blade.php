{{-- resources/views/admin/complaints/index.blade.php --}}
<x-app-layout>
  {{-- Header/topbar admin --}}
  <x-slot name="header">
    @include('profile.partials.top-menu-admin')
  </x-slot>

  {{-- Konten halaman --}}
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6 flex items-start justify-between gap-3">
      <div>
        <h1 class="text-2xl font-semibold">Manajemen Pengaduan</h1>
        <p class="text-sm text-slate-600">Pantau semua laporan yang masuk, gunakan filter untuk mempercepat penelusuran.</p>
      </div>

      {{-- Tombol Export (ikut filter aktif) --}}
      <div class="flex items-center gap-2">
        {{-- CSV standar (koma) --}}
        <a href="{{ route('admin.complaints.export.csv', request()->query()) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
           title="Export CSV (comma)">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 3v12m0 0l-4-4m4 4l4-4M5 20h14"/>
          </svg>
          <span class="font-medium">Export CSV</span>
          <span class="opacity-80">(,)</span>
        </a>

        {{-- CSV titik-koma --}}
        <a href="{{ route('admin.complaints.export.csv', array_merge(request()->query(), ['delimiter' => 'semicolon'])) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-indigo-600 bg-white px-4 py-2 text-indigo-700 shadow-sm hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
           title="Export CSV (semicolon)">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9l-6-6z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M14 3v6h6"/>
          </svg>
          <span class="font-medium">CSV (;)</span>
        </a>
      </div>
    </div>

    @if (session('status'))
      <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        {{ session('status') }}
      </div>
    @endif

    @php
      $statusLabels = [
        'submitted'  => 'Diajukan',
        'in_review'  => 'Ditinjau',
        'follow_up'  => 'Ditindaklanjuti',
        'closed'     => 'Selesai',
      ];
      $statusClasses = [
        'submitted'  => 'bg-slate-100 text-slate-700',
        'in_review'  => 'bg-amber-100 text-amber-800',
        'follow_up'  => 'bg-blue-100 text-blue-800',
        'closed'     => 'bg-emerald-100 text-emerald-800',
      ];
    @endphp

    {{-- Filter & Search --}}
    <form method="GET" action="{{ route('admin.complaints.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 mb-6">
      <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Cari</label>
        <input type="text" name="q" value="{{ request('q') }}"
               class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
               placeholder="Judul, deskripsi, nama/email pelapor">
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

    {{-- =========================
         DESKTOP TABLE (>= md)
       ========================= --}}
    <div class="hidden md:block">
      <div class="rounded-lg border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 text-slate-700">
            <tr>
              <th class="px-4 py-3 text-left font-medium">No</th>
              <th class="px-4 py-3 text-left font-medium">Judul</th>
              <th class="px-4 py-3 text-left font-medium">Pelapor</th>
              <th class="px-4 py-3 text-left font-medium">Status</th>
              <th class="px-4 py-3 text-left font-medium">Visibilitas</th>
              <th class="px-4 py-3 text-left font-medium">Dibuat</th>
              <th class="px-4 py-3 text-left font-medium">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
          @forelse ($complaints as $c)
            @php $badge = $statusClasses[$c->status] ?? 'bg-slate-100 text-slate-700'; @endphp
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3 whitespace-nowrap">
                {{ $loop->iteration + ($complaints->firstItem() - 1) }}
              </td>
              <td class="px-4 py-3">
                <div class="font-medium">{{ \Illuminate\Support\Str::limit($c->title, 60) }}</div>
                <div class="text-slate-500">{{ \Illuminate\Support\Str::limit($c->description, 80) }}</div>
              </td>
              <td class="px-4 py-3">
                <div class="font-medium">{{ optional($c->user)->name ?? '—' }}</div>
                <div class="text-slate-500 text-xs">{{ optional($c->user)->email ?? '' }}</div>
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badge }}">
                  {{ $statusLabels[$c->status] ?? ucfirst(str_replace('_',' ',$c->status)) }}
                </span>
              </td>
              <td class="px-4 py-3 capitalize">{{ str_replace('_',' ',$c->visibility) }}</td>
              <td class="px-4 py-3 whitespace-nowrap">
                {{ optional($c->created_at)->format('d M Y H:i') }}
                <div class="text-xs text-slate-500">{{ optional($c->created_at)->diffForHumans() }}</div>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <a href="{{ route('admin.complaints.show', $c) }}"
                     class="rounded-md border px-3 py-1.5 text-xs hover:bg-slate-50">Lihat</a>

                  <form method="POST" action="{{ route('admin.complaints.updateStatus', $c) }}"
                        onsubmit="return confirm('Ubah status laporan ini?')">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-center gap-1">
                      <select name="status"
                              class="rounded-md border-slate-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach ($statusLabels as $val => $label)
                          <option value="{{ $val }}" @selected($c->status===$val)>{{ $label }}</option>
                        @endforeach
                      </select>
                      <button class="rounded-md bg-slate-800 text-white text-xs px-2 py-1 hover:bg-slate-900">
                        Update
                      </button>
                    </div>
                    <input type="hidden" name="admin_note" value="">
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td class="px-4 py-8 text-center text-slate-500" colspan="7">Belum ada pengaduan.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- =====================
         MOBILE CARDS (< md)
       ===================== --}}
    <div class="md:hidden space-y-3">
      @forelse ($complaints as $c)
        @php $badge = $statusClasses[$c->status] ?? 'bg-slate-100 text-slate-700'; @endphp
        <div class="rounded-xl border border-slate-200 bg-white p-4">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-sm font-semibold text-slate-900">
                {{ \Illuminate\Support\Str::limit($c->title, 80) }}
              </div>
              <div class="mt-1 text-sm text-slate-600">
                {{ \Illuminate\Support\Str::limit($c->description, 120) }}
              </div>

              <div class="mt-2 flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badge }}">
                  {{ $statusLabels[$c->status] ?? ucfirst(str_replace('_',' ',$c->status)) }}
                </span>
                <span class="text-xs capitalize text-slate-600">• {{ str_replace('_',' ', $c->visibility) }}</span>
                <span class="text-xs text-slate-500">
                  • {{ optional($c->created_at)->format('d M Y H:i') }} ({{ optional($c->created_at)->diffForHumans() }})
                </span>
              </div>

              <div class="mt-1 text-xs text-slate-500">
                Pelapor: <span class="font-medium text-slate-700">{{ optional($c->user)->name ?? '—' }}</span>
                <span class="hidden xs:inline">• {{ optional($c->user)->email }}</span>
              </div>
            </div>

            <a href="{{ route('admin.complaints.show', $c) }}"
               class="shrink-0 rounded-md border px-3 py-1.5 text-xs hover:bg-slate-50">Lihat</a>
          </div>

          {{-- Quick update status (mobile) --}}
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
