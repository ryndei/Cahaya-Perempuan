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
    <div class="flex items-center justify-between">
      <h1 class="text-lg font-semibold">Riwayat Pengaduan Saya</h1>
      <a href="{{ route('complaints.create') }}"
         class="inline-flex rounded-lg bg-purple-700 px-4 py-2 font-semibold text-white hover:bg-purple-800">
        Buat Pengaduan
      </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 text-left">Kode</th>
            <th class="px-4 py-3 text-left">Judul</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Tanggal</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($complaints as $c)
            @php
              $badge = $statusClasses[$c->status] ?? 'bg-slate-100 text-slate-700';
            @endphp
            <tr class="border-t">
              <td class="px-4 py-3">{{ $c->code ?? $c->id }}</td>
              <td class="px-4 py-3">{{ $c->title }}</td>
              <td class="px-4 py-3">
                <span class="rounded-md px-2 py-1 text-xs {{ $badge }}">
                  {{ $statusLabels[$c->status] ?? ucfirst(str_replace('_',' ', $c->status)) }}
                </span>
              </td>
              <td class="px-4 py-3">
                {{ optional($c->created_at)->format('d M Y H:i') }}
              </td>
              <td class="px-4 py-3 text-right">
                <a class="text-purple-700 hover:underline" href="{{ route('complaints.show', $c) }}">Detail</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada data.</td>
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
