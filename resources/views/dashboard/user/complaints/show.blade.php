{{-- resources/views/dashboard/user/complaints/show.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <div class="print:hidden">@include('profile.partials.top-menu')</div>
  </x-slot>

  @php
    // Status (label + warna)
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

    // Lampiran
    $hasAttachment = filled($complaint->attachment_path ?? null);
    $ext = $hasAttachment ? strtolower(pathinfo($complaint->attachment_path, PATHINFO_EXTENSION)) : null;
    $isImage = $hasAttachment && in_array($ext, ['jpg','jpeg','png','gif','webp']);
    $attachmentUrl = $hasAttachment ? asset('storage/'.$complaint->attachment_path) : null;

    // Kode & tanggal
    $displayCode   = $complaint->code ?? $complaint->id;
    $createdAtText = optional($complaint->created_at)?->translatedFormat('d F Y H:i') ?? '—';
    $updatedAtText = optional($complaint->updated_at)?->translatedFormat('d F Y H:i') ?? '—';

    // Pelapor
    $reporterName  = $complaint->reporter_name ?: (optional($complaint->user)->name ?? '—');
    $reporterPhone = $complaint->reporter_phone ?: '—';
    $reporterAge   = $complaint->reporter_age ?? null;
    $reporterJob   = $complaint->reporter_job ?: '—';
    $disabilityDisplay = is_null($complaint->reporter_is_disability) ? '—' : ($complaint->reporter_is_disability ? 'Ya' : 'Tidak');

    // Lokasi pelapor (dukung city_* atau regency_*)
    $provinceName = $complaint->province_name ?? '—';
    $cityName     = $complaint->city_name ?? ($complaint->regency_name ?? '—');
    $districtName = $complaint->district_name ?? '—';
    $addressLine  = $complaint->reporter_address ?: '—';

    // Pelaku
    $perpetratorName = $complaint->perpetrator_name ?: '—';
    $perpetratorJob  = $complaint->perpetrator_job ?: '—';
    $perpetratorAge  = $complaint->perpetrator_age ?? null;
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
        <button type="button" onclick="window.print()"
                class="inline-flex items-center rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50">
          Cetak
        </button>
        @if ($hasAttachment)
          <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener"
             class="inline-flex items-center rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50">
            Buka Lampiran
          </a>
        @endif
      </div>
    </div>

    {{-- 2 kolom: Kiri (utama) + Kanan (ringkasan) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- KIRI: CARD UTAMA (detail + pelapor + pelaku) --}}
      <div class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <div class="mb-4 flex items-start justify-between gap-3">
            <div>
              <h1 class="text-xl font-semibold">Pengaduan #{{ $displayCode }}</h1>
              <p class="text-xs text-slate-500">
                Dikirim: {{ $createdAtText }}
                @if ($complaint->updated_at && $complaint->updated_at->ne($complaint->created_at))
                  • Diperbarui: {{ $updatedAtText }}
                @endif
              </p>
            </div>
            <span class="shrink-0 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 {{ $badge }}">
              {{ $statusLabels[$complaint->status] ?? ucfirst(str_replace('_',' ', $complaint->status)) }}
            </span>
          </div>

          {{-- Kategori & Deskripsi --}}
          @if ($complaint->category)
            <div class="mb-3">
              <p class="font-medium text-slate-800">Kategori</p>
              <p class="mt-1">{{ $complaint->category }}</p>
            </div>
          @endif

          <div class="mb-6">
            <p class="font-medium text-slate-800">Deskripsi</p>
            <p class="mt-1 whitespace-pre-line leading-relaxed text-slate-700">
              {{ $complaint->description }}
            </p>
          </div>

          {{-- Lampiran --}}
          @if ($hasAttachment)
            <div class="mb-6">
              <p class="font-medium text-slate-800">Lampiran</p>
              <div class="mt-2">
                @if ($isImage)
                  <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener" class="group block">
                    <img src="{{ $attachmentUrl }}" alt="Lampiran #{{ $displayCode }}"
                         class="max-h-72 rounded-lg border object-contain transition group-hover:opacity-90">
                    <span class="mt-1 block text-xs text-slate-500">Klik untuk membuka ukuran penuh</span>
                  </a>
                @else
                  <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener"
                     class="text-indigo-600 underline">Lihat lampiran ({{ strtoupper($ext) }})</a>
                @endif
              </div>
            </div>
          @endif

          {{-- ====== DATA PELAPOR & DATA PELAKU DALAM CARD UTAMA ====== --}}
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Sub-card: Data Pelapor --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
              <h3 class="text-sm font-semibold text-slate-800">Data Pelapor</h3>
              <dl class="mt-3 space-y-3 text-sm">
                <div class="grid grid-cols-3 gap-2">
                  <dt class="text-slate-500">Nama</dt>
                  <dd class="col-span-2 font-medium text-slate-800">{{ $reporterName }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-2">
                  <dt class="text-slate-500">Nomor HP</dt>
                  <dd class="col-span-2 font-medium text-slate-800">{{ $reporterPhone }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-2">
                  <dt class="text-slate-500">Umur</dt>
                  <dd class="col-span-2 font-medium text-slate-800">{{ $reporterAge ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-2">
                  <dt class="text-slate-500">Disabilitas</dt>
                  <dd class="col-span-2 font-medium text-slate-800">{{ $disabilityDisplay }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-2">
                  <dt class="text-slate-500">Pekerjaan</dt>
                  <dd class="col-span-2 font-medium text-slate-800">{{ $reporterJob }}</dd>
                </div>

                <div class="my-2 h-px bg-slate-200"></div>

                <div class="grid grid-cols-3 gap-2">
                  <dt class="text-slate-500">Provinsi</dt>
                  <dd class="col-span-2 font-medium text-slate-800">{{ $provinceName }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-2">
                  <dt class="text-slate-500">Kota/Kab.</dt>
                  <dd class="col-span-2 font-medium text-slate-800">{{ $cityName }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-2">
                  <dt class="text-slate-500">Kecamatan</dt>
                  <dd class="col-span-2 font-medium text-slate-800">{{ $districtName }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-2">
                  <dt class="text-slate-500">Alamat</dt>
                  <dd class="col-span-2 font-medium text-slate-800 whitespace-pre-line">{{ $addressLine }}</dd>
                </div>
              </dl>
            </div>

            {{-- Sub-card: Data Pelaku --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
              <h3 class="text-sm font-semibold text-slate-800">Data Pelaku</h3>
              <dl class="mt-3 space-y-3 text-sm">
                <div class="grid grid-cols-3 gap-2">
                  <dt class="text-slate-500">Nama</dt>
                  <dd class="col-span-2 font-medium text-slate-800">{{ $perpetratorName }}</dd>
                </div>
                @if (!is_null($perpetratorAge))
                  <div class="grid grid-cols-3 gap-2">
                    <dt class="text-slate-500">Umur</dt>
                    <dd class="col-span-2 font-medium text-slate-800">{{ $perpetratorAge }}</dd>
                  </div>
                @endif
                <div class="grid grid-cols-3 gap-2">
                  <dt class="text-slate-500">Pekerjaan</dt>
                  <dd class="col-span-2 font-medium text-slate-800">{{ $perpetratorJob }}</dd>
                </div>
              </dl>
            </div>
          </div>
          {{-- ====== /END SUB-SECTION ====== --}}

          {{-- Pesan admin (jika ada) --}}
          @if (!empty($complaint->admin_note))
            <div class="mt-6 rounded-xl border border-indigo-200 bg-indigo-50 p-4">
              <h3 class="text-sm font-semibold text-indigo-900">Pesan dari Admin</h3>
              <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-indigo-900">
                {{ $complaint->admin_note }}
              </p>
              <p class="mt-2 text-xs text-indigo-900/70">
                Terakhir diperbarui: {{ optional($complaint->updated_at)->diffForHumans() }}
              </p>
            </div>
          @else
            <div class="mt-6 rounded-xl border border-slate-200 bg-white p-4">
              <p class="text-sm text-slate-600">Belum ada pesan dari admin untuk laporan ini.</p>
            </div>
          @endif
        </div>
      </div>

      {{-- KANAN: Ringkasan singkat --}}
      <div>
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
              <dt class="col-span-1 text-slate-500">Dikirim</dt>
              <dd class="col-span-2">{{ $createdAtText }}</dd>
            </div>
          </dl>

          <div class="mt-6">
            <a href="{{ route('complaints.index') }}"
               class="inline-flex items-center rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50">
              ← Kembali ke Riwayat
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
