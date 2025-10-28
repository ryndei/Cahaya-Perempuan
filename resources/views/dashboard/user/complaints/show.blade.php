{{-- resources/views/dashboard/user/complaints/show.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <div class="print:hidden">@include('profile.partials.top-menu')</div>
  </x-slot>

  <div class="max-w-5xl mx-auto p-6 space-y-6">
    {{-- Breadcrumb & Aksi --}}
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('complaints.index') }}" class="hover:underline">Riwayat</a>
        <span>/</span>
        <span class="text-slate-700">Pengaduan #{{ $displayCode }}</span>
      </div>

      <div class="flex items-center gap-2 print:hidden">
        <button type="button" onclick="window.print()"
                class="inline-flex items-center rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50">
          Cetak
        </button>
      </div>
    </div>

    {{-- 2 kolom: Kiri (utama) + Kanan (ringkasan) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- KIRI: CARD UTAMA --}}
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
              <p class="mt-1 whitespace-pre-line break-words hyphens-auto leading-relaxed text-slate-700">
            {{ $complaint->description }}
              </p>
          </div>

          {{-- ====== DATA PELAPOR & DATA PELAKU ====== --}}
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Data Pelapor --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
              <h3 class="text-sm font-semibold text-slate-800">Data Pelapor</h3>
              <dl class="mt-3 space-y-3 text-sm">
                @foreach ($reporterRows as [$label, $value])
                  <div class="grid grid-cols-3 gap-2">
                    <dt class="text-slate-500">{{ $label }}</dt>
                    <dd class="col-span-2 font-medium text-slate-800">{{ $value }}</dd>
                  </div>
                @endforeach

                <div class="my-2 h-px bg-slate-200"></div>

                @foreach ($locationRows as [$label, $value])
                  <div class="grid grid-cols-3 gap-2">
                    <dt class="text-slate-500">{{ $label }}</dt>
                    <dd class="col-span-2 font-medium text-slate-800 {{ $label === 'Alamat' ? 'whitespace-pre-line' : '' }}">
                      {{ $value }}
                    </dd>
                  </div>
                @endforeach
              </dl>
            </div>

            {{-- Data Pelaku --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
              <h3 class="text-sm font-semibold text-slate-800">Data Pelaku</h3>
              <dl class="mt-3 space-y-3 text-sm">
                @foreach ($perpetratorRows as [$label, $value])
                  <div class="grid grid-cols-3 gap-2">
                    <dt class="text-slate-500">{{ $label }}</dt>
                    <dd class="col-span-2 font-medium text-slate-800">{{ $value }}</dd>
                  </div>
                @endforeach
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
               class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300
                      bg-white px-5 py-2.5 font-semibold text-slate-800 hover:bg-slate-50
                      focus:outline-none focus:ring-2 focus:ring-purple-300">
              ← Kembali ke Riwayat
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
