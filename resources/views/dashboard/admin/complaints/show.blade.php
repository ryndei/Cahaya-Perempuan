{{-- resources/views/admin/complaints/show.blade.php --}}
<x-app-layout>
  {{-- Header (tidak ikut tercetak) --}}
  <x-slot name="header">
    <div class="no-print">
      @include('profile.partials.top-menu-admin')
    </div>
  </x-slot>

  {{-- === PRINT STYLES === --}}
  <style>
    .only-print { display: none; }
    .no-print { display: initial; }
    @media print {
      @page { size: A4; margin: 16mm; }
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      .no-print { display: none !important; }
      .only-print { display: block !important; }
      .print-flat, .print-card { box-shadow: none !important; border: 1px solid #111 !important; border-radius: 0 !important; }
      .print-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12mm; }
      .signature-box, .stamp-box { height: 35mm; border: 1px dashed #999; position: relative; padding: 8mm; }
      .signature-box .label, .stamp-box .label { position: absolute; top: -10px; left: 8mm; background: #fff; padding: 0 4mm; font-weight: 600; }
      .print-title { font-size: 18px; font-weight: 800; margin: 0; }
      .print-subtitle { margin-top: 2px; font-size: 12px; color: #334155; }
      .print-hr { border: 0; border-top: 1px solid #000; margin: 8px 0 12px 0; }
      .page-break { break-before: page; }
    }
  </style>


  <div class="mx-auto max-w-6xl p-6 space-y-6">
    @if (session('status'))
      <div class="no-print rounded-lg bg-green-50 px-4 py-3 text-green-700">{{ session('status') }}</div>
    @endif
    @if (session('success'))
      <div class="no-print rounded-lg bg-emerald-50 px-4 py-3 text-emerald-800">{{ session('success') }}</div>
    @endif

    {{-- ====== GRID UTAMA ====== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- KIRI: Rincian --}}
      <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 overflow-hidden">
        {{-- HEADER CARD --}}
        <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
          <div class="min-w-0">
            <h1 class="text-lg font-semibold break-words">
              Pengaduan #{{ $complaint->code ?? $complaint->id }}
            </h1>
            <p class="text-xs text-slate-500">
              Dibuat: {{ $createdAtText }} • Diperbarui: {{ $updatedAtText }}
            </p>
          </div>

          <div class="shrink-0 flex flex-col items-end gap-1">
            <div class="flex items-center gap-2">
              <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badge }}">
                {{ $statusLabel }}
              </span>

              <button type="button" onclick="window.print()"
                class="no-print inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-sm font-semibold
                       border border-slate-300 text-slate-700 hover:bg-slate-50">
                Cetak
              </button>
            </div>

            {{-- Info pengubah terakhir --}}
            @if($lastChanger || $lastChangedAtText)
              <div class="text-[11px] text-slate-500 leading-4">
                Terakhir diubah
                @if($lastChanger) oleh <span class="font-medium text-slate-700">{{ $lastChanger }}</span>@endif
                @if($lastChangedAtText) • <span title="{{ $lastChangedAtText }}">{{ $lastChangedDiff }}</span>@endif
              </div>
            @endif
          </div>
        </div>

        <dl class="text-sm space-y-4">
          @if($complaint->category)
            <div>
              <dt class="font-medium text-slate-700">Kategori</dt>
              <dd class="text-slate-800">{{ $complaint->category }}</dd>
            </div>
          @endif

          <div>
            <dt class="font-medium text-slate-700">Lokasi</dt>
            <dd class="text-slate-800">{{ $lokasiRingkas ?: '—' }}</dd>
          </div>

          <div>
            <dt class="font-medium text-slate-700">Alamat Spesifik</dt>
            <dd class="text-slate-800 whitespace-pre-line break-words">{{ $complaint->reporter_address ?: '—' }}</dd>
          </div>

          <div>
            <dt class="font-medium text-slate-700">Deskripsi</dt>
            {{-- Cegah overflow teks panjang --}}
            <dd class="text-slate-800 whitespace-pre-line break-words">
              {{ $complaint->description }}
            </dd>
          </div>
        </dl>
      </div>

      {{-- KANAN: Data Pelapor & Pelaku + Riwayat --}}
      <div class="space-y-6">
        {{-- Data Pelapor --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <h2 class="text-sm font-semibold text-slate-700">Data Pelapor</h2>
          <dl class="mt-3 space-y-3 text-sm">
            <div class="grid grid-cols-3 gap-2">
              <dt class="text-slate-500">Akun</dt>
              <dd class="col-span-2 text-slate-800 break-words">
                {{ optional($complaint->user)->name ?? '—' }}
                <span class="text-slate-500">({{ optional($complaint->user)->email ?? '—' }})</span>
              </dd>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <dt class="text-slate-500">Nama</dt>
              <dd class="col-span-2 text-slate-800 break-words">{{ $complaint->reporter_name ?: '—' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <dt class="text-slate-500">Nomor HP</dt>
              <dd class="col-span-2 text-slate-800 break-words">{{ $complaint->reporter_phone ?: '—' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <dt class="text-slate-500">Umur</dt>
              <dd class="col-span-2 text-slate-800">{{ $complaint->reporter_age ?? '—' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <dt class="text-slate-500">Disabilitas</dt>
              <dd class="col-span-2 text-slate-800">{{ $disabilityText }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <dt class="text-slate-500">Pekerjaan</dt>
              <dd class="col-span-2 text-slate-800 break-words">{{ $complaint->reporter_job ?: '—' }}</dd>
            </div>
            <hr class="my-2 border-slate-200">
            <div class="grid grid-cols-3 gap-2">
              <dt class="text-slate-500">Provinsi</dt>
              <dd class="col-span-2 text-slate-800 break-words">{{ $complaint->province_name ?: '—' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <dt class="text-slate-500">Kab/Kota</dt>
              <dd class="col-span-2 text-slate-800 break-words">{{ $complaint->regency_name ?: '—' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <dt class="text-slate-500">Kecamatan</dt>
              <dd class="col-span-2 text-slate-800 break-words">{{ $complaint->district_name ?: '—' }}</dd>
            </div>
          </dl>
        </div>

        {{-- Data Pelaku --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <h2 class="text-sm font-semibold text-slate-700">Data Pelaku</h2>
          <dl class="mt-3 space-y-3 text-sm">
            <div class="grid grid-cols-3 gap-2">
              <dt class="text-slate-500">Nama</dt>
              <dd class="col-span-2 text-slate-800 break-words">{{ $complaint->perpetrator_name ?: '—' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <dt class="text-slate-500">Umur</dt>
              <dd class="col-span-2 text-slate-800">{{ $complaint->perpetrator_age ?? '—' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <dt class="text-slate-500">Pekerjaan</dt>
              <dd class="col-span-2 text-slate-800 break-words">{{ $complaint->perpetrator_job ?: '—' }}</dd>
            </div>
          </dl>
        </div>

        {{-- Riwayat Status--}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <h2 class="text-sm font-semibold text-slate-700">Riwayat Status</h2>
          @if($history->isEmpty())
            <p class="mt-3 text-sm text-slate-500">Belum ada perubahan status.</p>
          @else
            <ol class="mt-3 space-y-3">
              @foreach ($history as $act)
                @php
                  $p = $act->properties ?? collect();
                  $from = $p['from_label'] ?? $p['from_label'] ?? null;
                  $to   = $p['to_label']   ?? $p['to_label']   ?? null;
                  $by   = optional($act->causer)->name ?? optional($act->causer)->email;
                  $at   = optional($act->created_at);
                @endphp
                <li class="text-sm">
                  <div class="flex items-start gap-2">
                    <div class="mt-1 h-2 w-2 rounded-full bg-slate-400"></div>
                    <div class="flex-1 min-w-0">
                      <div class="text-slate-800">
                        @if($from || $to)
                          <span class="font-medium">{{ $from ?: '—' }}</span>
                          <span class="mx-1">→</span>
                          <span class="font-medium">{{ $to ?: '—' }}</span>
                        @else
                          {{ $act->description ?? 'Perubahan status' }}
                        @endif
                      </div>
                      <div class="text-xs text-slate-500">
                        @if($by) Oleh <span class="font-medium text-slate-700">{{ $by }}</span>@endif
                        @if($at) • {{ $at->translatedFormat('d M Y H:i') }} ({{ $at->diffForHumans() }}) @endif
                      </div>
                    </div>
                  </div>
                </li>
              @endforeach
            </ol>
          @endif
        </div>
      </div>
    </div>

    {{-- ====== TINDAKAN ADMIN (tidak ikut tercetak) ====== --}}
    <div class="print:hidden rounded-2xl border border-slate-200 bg-white p-6">
      <h2 class="text-sm font-medium mb-4 text-slate-700">Tindakan Admin</h2>
      <form method="POST" action="{{ route('admin.complaints.updateStatus', $complaint) }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          @php($statusLabels = \App\Models\Complaint::statusLabels())
          <div>
            <label class="block text-sm font-medium text-slate-700">Status</label>
            <select name="status" class="mt-1 w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
              @foreach ($statusLabels as $val => $label)
                <option value="{{ $val }}" @selected(old('status', $complaint->status) === $val)>{{ $label }}</option>
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
          <button type="submit"
            class="inline-flex items-center justify-center rounded-lg px-4 py-2 font-semibold leading-5 text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Simpan Perubahan
          </button>
          <a href="{{ route('admin.complaints.index') }}"
            class="inline-flex items-center justify-center rounded-lg px-4 py-2 font-semibold leading-5 text-white bg-slate-700 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-600 focus:ring-offset-2">
            Kembali
          </a>
        </div>
      </form>
    </div>

    {{-- ====== KOP SURAT (PRINT ONLY) ====== --}}
    <div class="only-print print-flat" style="padding: 4mm; margin-bottom: 6mm;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div>
          <h2 class="print-title">{{ config('app.name') }}</h2>
          <div class="print-subtitle">
            Jl. Indragiri 1 No. 03, Kota Bengkulu • WA: 0823-0673-8686 • Email: cp.wccbengkulu@gmail.com
          </div>
        </div>
      </div>
      <hr class="print-hr">
    </div>

    {{-- ====== RINGKASAN CETAK (PRINT ONLY) ====== --}}
    <div class="only-print print-card" style="padding: 8mm; margin-bottom: 8mm;">
      <div style="font-size: 14px; font-weight:700; margin-bottom:6px;">Pengaduan #{{ $complaint->code ?? $complaint->id }}</div>
      <div style="font-size:12px; line-height:1.5;">
        @if($complaint->category)<div><strong>Kategori:</strong> {{ $complaint->category }}</div>@endif
        <div><strong>Dibuat:</strong> {{ $createdAtText }} • <strong>Diperbarui:</strong> {{ $updatedAtText }}</div>
        <div><strong>Lokasi:</strong> {{ $lokasiRingkas ?: '—' }}</div>
        <div><strong>Alamat:</strong> {{ $complaint->reporter_address ?: '—' }}</div>

        <div style="margin-top:6px;"><strong>Data Pelapor:</strong>
          <div>Nama: {{ $complaint->reporter_name ?: '—' }} | Umur: {{ $complaint->reporter_age ?? '—' }} | Disabilitas: {{ $disabilityText }}</div>
          <div>HP: {{ $complaint->reporter_phone ?: '—' }} | Pekerjaan: {{ $complaint->reporter_job ?: '—' }}</div>
        </div>

        <div style="margin-top:6px;"><strong>Data Pelaku:</strong>
          <div>Nama: {{ $complaint->perpetrator_name ?: '—' }} | Umur: {{ $complaint->perpetrator_age ?? '—' }} | Pekerjaan: {{ $complaint->perpetrator_job ?: '—' }}</div>
        </div>

        <div style="margin-top:6px;"><strong>Deskripsi:</strong></div>
        <div style="white-space:pre-line; margin-top:2px;">{{ \Illuminate\Support\Str::limit($complaint->description, 1200) }}</div>
      </div>
    </div>

    {{-- ====== AREA TTD & MATERAI (PRINT ONLY) ====== --}}
    <div class="only-print">
      <div class="print-grid-2">
        <div class="signature-box">
          <div class="label">Tanda Tangan Petugas</div>
          <div style="position:absolute; bottom:8mm; left:8mm; right:8mm; font-size:12px;">
            Nama Jelas: ________________________________
            <div style="margin-top:6px;">Tanggal: ____ / ____ / ______</div>
          </div>
        </div>

        <div class="stamp-box">
          <div class="label">Materai (Rp10.000)</div>
          <div style="display:flex; justify-content:center; align-items:center; height:100%; font-size:12px;">
            Tempel materai di area ini
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
