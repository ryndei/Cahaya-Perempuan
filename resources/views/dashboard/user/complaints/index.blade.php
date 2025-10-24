{{-- resources/views/dashboard/user/complaints/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">@include('profile.partials.top-menu')</x-slot>

  @php
    $statusLabels = [
      'submitted'  => 'Diajukan',
      'in_review'  => 'Ditinjau',
      'follow_up'  => 'Ditindaklanjuti',
      'closed'     => 'Selesai',
    ];
    $statusClasses = [
      'submitted'  => 'bg-yellow-100 text-yellow-800',
      'in_review'  => 'bg-blue-100 text-blue-800',
      'follow_up'  => 'bg-amber-100 text-amber-800',
      'closed'     => 'bg-green-100 text-green-800',
    ];
  @endphp

  <div class="max-w-5xl mx-auto p-6 space-y-4">
    <div class="flex items-center justify-between gap-3">
      <h1 class="text-lg font-semibold">Riwayat Pengaduan Saya</h1>
      <a href="{{ route('complaints.create') }}"
         class="inline-flex rounded-lg bg-purple-700 px-4 py-2 font-semibold text-white hover:bg-purple-800">
        Buat Pengaduan
      </a>
    </div>

    {{-- MOBILE: Kartu (md:hidden) --}}
    <div class="space-y-3 md:hidden">
      @forelse ($complaints as $c)
        @php $badge = $statusClasses[$c->status] ?? 'bg-slate-100 text-slate-700'; @endphp
        <div class="rounded-xl border border-slate-200 bg-white p-4">
          <div class="flex items-start justify-between gap-2">
            <div>
              <div class="text-xs text-slate-500">Kode</div>
              <div class="font-semibold">{{ $c->code ?? $c->id }}</div>
            </div>
            <span class="shrink-0 rounded-md px-2 py-1 text-[11px] font-medium {{ $badge }}">
              {{ $statusLabels[$c->status] ?? ucfirst(str_replace('_',' ', $c->status)) }}
            </span>
          </div>

          <div class="mt-2">
            <div class="text-xs text-slate-500">Judul</div>
            <div class="text-sm font-medium text-slate-900">{{ $c->title }}</div>
          </div>

          <div class="mt-2 grid grid-cols-2 gap-2">
            <div>
              <div class="text-xs text-slate-500">Dibuat</div>
              <div class="text-sm">
                {{ optional($c->created_at)?->translatedFormat('d M Y H:i') }}
              </div>
              <div class="text-xs text-slate-500">
                {{ optional($c->created_at)?->diffForHumans() }}
              </div>
            </div>
            @if($c->category)
              <div>
                <div class="text-xs text-slate-500">Kategori</div>
                <div class="text-sm">{{ $c->category }}</div>
              </div>
            @endif
          </div>

          <div class="mt-3 text-right">
            <a class="inline-flex rounded-md border px-3 py-1.5 text-xs font-semibold hover:bg-slate-50"
               href="{{ route('complaints.show', $c) }}">
              Detail
            </a>
          </div>
        </div>
      @empty
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center">
          <div class="text-slate-600">Belum ada pengaduan.</div>
          <a href="{{ route('complaints.create') }}"
             class="mt-3 inline-flex rounded-lg bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800">
            Buat Pengaduan
          </a>
        </div>
      @endforelse
    </div>

    {{-- DESKTOP: Tabel (hidden di mobile) --}}
    <div class="hidden md:block rounded-2xl border border-slate-200 bg-white overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 text-left">Kode</th>
            <th class="px-4 py-3 text-left">Kategori</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Tanggal</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($complaints as $c)
            @php $badge = $statusClasses[$c->status] ?? 'bg-slate-100 text-slate-700'; @endphp
            <tr class="border-t">
              <td class="px-4 py-3 whitespace-nowrap">{{ $c->code ?? $c->id }}</td>
              <td class="px-4 py-3">{{ $c->category ?: '—' }}</td>
              <td class="px-4 py-3">
                <span class="rounded-md px-2 py-1 text-xs {{ $badge }}">
                  {{ $statusLabels[$c->status] ?? ucfirst(str_replace('_',' ', $c->status)) }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                {{ optional($c->created_at)?->translatedFormat('d M Y H:i') }}
                <div class="text-xs text-slate-500">
                  {{ optional($c->created_at)?->diffForHumans() }}
                </div>
              </td>
              <td class="px-4 py-3 text-right">
                <a class="text-purple-700 hover:underline" href="{{ route('complaints.show', $c) }}">Detail</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-6 text-center text-slate-500">Belum ada data.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div>
      {{ $complaints->links() }}
    </div>
  </div>
</x-app-layout>
