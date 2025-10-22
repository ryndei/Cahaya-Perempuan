{{-- resources/views/admin/complaints/show.blade.php --}}
<x-app-layout>
  <x-slot name="header">@include('profile.partials.top-menu-admin')</x-slot>

  @php
    // Label & warna status (Indonesia)
    $statusLabels = [
      'submitted'  => 'Diajukan',
      'in_review'  => 'Ditinjau',
      'follow_up'  => 'Ditindaklanjuti',
      'closed'     => 'Selesai',
    ];
    $statusClasses = [
      'submitted'  => 'bg-slate-100 text-slate-800',
      'in_review'  => 'bg-amber-100 text-amber-800',
      'follow_up'  => 'bg-blue-100 text-blue-800',
      'closed'     => 'bg-emerald-100 text-emerald-800',
    ];
    $badge = $statusClasses[$complaint->status] ?? 'bg-slate-100 text-slate-800';
  @endphp

  <div class="max-w-4xl mx-auto p-6 space-y-6">
    @if (session('status'))
      <div class="rounded-lg bg-green-50 px-4 py-3 text-green-700">{{ session('status') }}</div>
    @endif

    {{-- Kartu detail --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6">
      <div class="mb-4 flex items-start justify-between gap-3">
        <div>
          <h1 class="text-lg font-semibold">
            Pengaduan #{{ $complaint->code ?? $complaint->id }}
          </h1>
          <p class="text-xs text-slate-500">
            Dibuat: {{ optional($complaint->created_at)->format('d M Y H:i') }}
            • Diperbarui: {{ optional($complaint->updated_at)->format('d M Y H:i') }}
          </p>
        </div>
        <span class="shrink-0 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badge }}">
          {{ $statusLabels[$complaint->status] ?? ucfirst(str_replace('_',' ', $complaint->status)) }}
        </span>
      </div>

      <dl class="text-sm space-y-3">
        <div>
          <dt class="font-medium text-slate-700">Pelapor</dt>
          <dd class="text-slate-700">
            {{ optional($complaint->user)->name ?? '—' }}
            <span class="text-slate-500">({{ optional($complaint->user)->email ?? '—' }})</span>
          </dd>
        </div>

        <div>
          <dt class="font-medium text-slate-700">Visibilitas</dt>
          <dd class="capitalize text-slate-700">{{ str_replace('_',' ', $complaint->visibility) }}</dd>
        </div>
<div>
      <dt class="text-slate-500">Nama</dt>
      <dd class="font-medium">{{ $complaint->reporter_name ?: '—' }}</dd>
    </div>
    <div>
      <dt class="text-slate-500">No. Telepon</dt>
      <dd class="font-medium">{{ $complaint->reporter_phone ?: '—' }}</dd>
    </div>
    <div class="sm:col-span-3">
      <dt class="text-slate-500">Alamat</dt>
      <dd class="font-medium">{{ $complaint->reporter_address ?: '—' }}</dd>
    </div>
        <div>
          <dt class="font-medium text-slate-700">Judul</dt>
          <dd>{{ $complaint->title }}</dd>
        </div>

        @if($complaint->category)
          <div>
            <dt class="font-medium text-slate-700">Kategori</dt>
            <dd>{{ $complaint->category }}</dd>
          </div>
        @endif

        <div>
          <dt class="font-medium text-slate-700">Deskripsi</dt>
          <dd class="whitespace-pre-line">{{ $complaint->description }}</dd>
        </div>

        @if($complaint->attachment_path)
          <div>
            <dt class="font-medium text-slate-700">Lampiran</dt>
            <dd>
              <a class="text-indigo-600 underline"
                 href="{{ asset('storage/'.$complaint->attachment_path) }}"
                 target="_blank" rel="noopener">Lihat lampiran</a>
            </dd>
          </div>
        @endif
      </dl>

     
    </div>

    {{-- Kartu tindakan admin --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6">
      <h2 class="text-sm font-medium mb-4 text-slate-700">Tindakan Admin</h2>

      <form method="POST" action="{{ route('admin.complaints.updateStatus', $complaint) }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700">Ubah Status</label>
            <select name="status" class="mt-1 w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
              @foreach ($statusLabels as $val => $label)
                <option value="{{ $val }}" @selected($complaint->status===$val)>{{ $label }}</option>
              @endforeach
            </select>
            @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Catatan (opsional)</label>
            <textarea name="admin_note" rows="4"
                      class="mt-1 w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('admin_note', $complaint->admin_note) }}</textarea>
            @error('admin_note') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2">
  {{-- Simpan --}}
  <button type="submit"
          class="inline-flex items-center justify-center rounded-lg px-4 py-2 font-semibold leading-5
                 text-white bg-indigo-600 hover:bg-indigo-700
                 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
    Simpan Perubahan
  </button>

  {{-- Kembali --}}
  <a href="{{ route('admin.complaints.index') }}"
     role="button"
     class="inline-flex items-center justify-center rounded-lg px-4 py-2 font-semibold leading-5
            text-white bg-indigo-600 hover:bg-indigo-700
            focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
    Kembali
  </a>
</div>
     
      </form>
    </div>
  </div>
</x-app-layout>
