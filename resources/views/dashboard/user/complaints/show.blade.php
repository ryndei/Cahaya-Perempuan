{{-- resources/views/dashboard/user/complaints/show.blade.php --}}
<x-app-layout>
  <x-slot name="header">@include('profile.partials.top-menu')</x-slot>

  @php
    // Label & warna status (Indonesia)
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
    $badge = $statusClasses[$complaint->status] ?? 'bg-slate-100 text-slate-800 ring-slate-200';

    // Visibilitas
    $visibilityLabels = [
      'private' => 'Pribadi',
      'shared_agencies' => 'Dibagikan ke Instansi Terkait',
    ];

    // Lampiran
    $hasAttachment = filled($complaint->attachment_path ?? null);
    $ext = $hasAttachment ? strtolower(pathinfo($complaint->attachment_path, PATHINFO_EXTENSION)) : null;
    $isImage = $hasAttachment && in_array($ext, ['jpg','jpeg','png','gif','webp']);
    $attachmentUrl = $hasAttachment ? asset('storage/'.$complaint->attachment_path) : null;

    // Kode fallback
    $displayCode = $complaint->code ?? $complaint->id;

    // Data pelapor dari form (dengan fallback aman)
    $reporterName    = $complaint->reporter_name    ?: (optional($complaint->user)->name ?? '—');
    $reporterPhone   = $complaint->reporter_phone   ?: '—';
    // Gunakan reporter_address bila ada; fallback ke kolom "location" jika Anda masih memakainya.
    $reporterAddress = $complaint->reporter_address ?: ($complaint->location ?? '—');
  @endphp

  <div class="max-w-5xl mx-auto p-6 space-y-6">
    {{-- Breadcrumb & Aksi --}}
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('complaints.index') }}" class="hover:underline">Riwayat</a>
        <span>/</span>
        <span class="text-slate-700">Pengaduan #{{ $displayCode }}</span>
      </div>

      <div class="flex items-center gap-2">
        <button
          type="button"
          onclick="window.print()"
          class="inline-flex items-center rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50">
          Cetak
        </button>
        @if($hasAttachment)
          <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener"
             class="inline-flex items-center rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50">
            Buka Lampiran
          </a>
        @endif
      </div>
    </div>

    {{-- Layout 2 kolom --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- Konten utama --}}
      <div class="lg:col-span-2 space-y-6">
        {{-- Detail laporan --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <div class="mb-4 flex items-start justify-between gap-3">
            <div>
              <h1 class="text-xl font-semibold">Pengaduan #{{ $displayCode }}</h1>
              <p class="text-xs text-slate-500">
                Dikirim: {{ optional($complaint->created_at)->format('d M Y H:i') }}
                @if($complaint->updated_at && $complaint->updated_at->ne($complaint->created_at))
                  • Diperbarui: {{ optional($complaint->updated_at)->format('d M Y H:i') }}
                @endif
              </p>
            </div>
            <span class="shrink-0 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 {{ $badge }}">
              {{ $statusLabels[$complaint->status] ?? ucfirst(str_replace('_',' ', $complaint->status)) }}
            </span>
          </div>

          <div class="space-y-5 text-sm">
            <div>
              <p class="font-medium text-slate-800">Judul</p>
              <p class="mt-1">{{ $complaint->title }}</p>
            </div>

            @if ($complaint->category)
              <div>
                <p class="font-medium text-slate-800">Kategori</p>
                <p class="mt-1">{{ $complaint->category }}</p>
              </div>
            @endif

            <div>
              <p class="font-medium text-slate-800">Deskripsi</p>
              <p class="mt-1 whitespace-pre-line leading-relaxed text-slate-700">
                {{ $complaint->description }}
              </p>
            </div>

            @if($hasAttachment)
              <div>
                <p class="font-medium text-slate-800">Lampiran</p>
                <div class="mt-2">
                  @if($isImage)
                    <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener" class="group block">
                      <img
                        src="{{ $attachmentUrl }}"
                        alt="Lampiran pengaduan #{{ $displayCode }}"
                        class="max-h-72 rounded-lg border object-contain transition group-hover:opacity-90">
                      <span class="mt-1 block text-xs text-slate-500">Klik untuk membuka ukuran penuh</span>
                    </a>
                  @else
                    <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener"
                       class="text-indigo-600 underline">
                      Lihat lampiran ({{ strtoupper($ext) }})
                    </a>
                  @endif
                </div>
              </div>
            @endif
          </div>
        </div>

        {{-- PESAN DARI ADMIN --}}
        @if(!empty($complaint->admin_note))
          <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-6">
            <div class="flex items-start gap-3">
              <div class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-indigo-500"></div>
              <div class="flex-1">
                <h2 class="text-sm font-semibold text-indigo-900">Pesan dari Admin</h2>
                <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-indigo-900">
                  {{ $complaint->admin_note }}
                </p>
                <p class="mt-2 text-xs text-indigo-900/70">
                  Terakhir diperbarui: {{ optional($complaint->updated_at)->diffForHumans() }}
                </p>
              </div>
            </div>
          </div>
        @else
          <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-sm text-slate-600">Belum ada pesan dari admin untuk laporan ini.</p>
          </div>
        @endif
      </div>

      {{-- Sidebar meta + Data Pelapor --}}
      <div class="space-y-6">
        {{-- Ringkasan --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <h2 class="text-sm font-semibold text-slate-700">Ringkasan</h2>
          <dl class="mt-4 space-y-4 text-sm">
            <div class="grid grid-cols-3 gap-2">
              <dt class="col-span-1 text-slate-500">Kode</dt>
              <dd class="col-span-2 font-medium">{{ $displayCode }}</dd>
            </div>

            <div class="grid grid-cols-3 gap-2">
              <dt class="col-span-1 text-slate-500">Status</dt>
              <dd class="col-span-2">
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 {{ $badge }}">
                  {{ $statusLabels[$complaint->status] ?? ucfirst(str_replace('_',' ', $complaint->status)) }}
                </span>
              </dd>
            </div>

            <div class="grid grid-cols-3 gap-2">
              <dt class="col-span-1 text-slate-500">Visibilitas</dt>
              <dd class="col-span-2">{{ $visibilityLabels[$complaint->visibility] ?? ucfirst($complaint->visibility) }}</dd>
            </div>
          </dl>

          <div class="mt-6">
            <a href="{{ route('complaints.index') }}"
               class="inline-flex items-center rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50">
              ← Kembali ke Riwayat
            </a>
          </div>
        </div>

        {{-- Data Pelapor --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <h2 class="text-sm font-semibold text-slate-700">Data Pelapor</h2>
          <dl class="mt-4 space-y-4 text-sm">
            <div class="grid grid-cols-3 gap-2">
              <dt class="col-span-1 text-slate-500">Nama</dt>
              <dd class="col-span-2 font-medium text-slate-800">{{ $reporterName }}</dd>
            </div>

            <div class="grid grid-cols-3 gap-2">
              <dt class="col-span-1 text-slate-500">Nomor HP</dt>
              <dd class="col-span-2 font-medium text-slate-800">{{ $reporterPhone }}</dd>
            </div>

            <div class="grid grid-cols-3 gap-2">
              <dt class="col-span-1 text-slate-500">Alamat/Lokasi</dt>
              <dd class="col-span-2 font-medium text-slate-800 whitespace-pre-line">
                {{ $reporterAddress }}
              </dd>
            </div>
          </dl>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
