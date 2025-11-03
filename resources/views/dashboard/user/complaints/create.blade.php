{{-- resources/views/dashboard/user/complaints/create.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    @include('profile.partials.top-menu')
  </x-slot>

  <div class="max-w-3xl mx-auto p-6">
    {{-- Global alert form error --}}
    @if ($errors->any())
      <div class="mb-4 rounded border border-rose-200 bg-rose-50 px-4 py-2 text-rose-700">
        <div class="font-semibold">Form tidak dapat dikirim:</div>
        <ul class="list-disc pl-5">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Flash status --}}
    @if (session('status'))
      <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-green-700">
        {{ session('status') }}
      </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6">
      <h1 class="mb-4 text-lg font-semibold">Buat Pengaduan</h1>

      <form method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- ============ DATA PELAPOR ============ --}}
        <section>
          <h2 class="text-sm font-semibold text-slate-800">Data Pelapor</h2>

          <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <label class="block text-sm font-medium text-slate-700">Nama Pelapor</label>
              <input
                type="text"
                name="reporter_name"
                value="{{ old('reporter_name') }}"
                class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
              />
              @error('reporter_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700">No. Telepon</label>
              <input
                type="text"
                name="reporter_phone"
                value="{{ old('reporter_phone') }}"
                placeholder="08xxxxxxxxxx / +62xxxx"
                class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
              />
              @error('reporter_phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700">Umur Pelapor</label>
              <input
                type="number"
                name="reporter_age"
                min="0" max="120" step="1"
                value="{{ old('reporter_age') }}"
                class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
              />
              @error('reporter_age') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700">Penyandang Disabilitas</label>
              <select
                name="reporter_is_disability"
                class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
              >
                <option value="" @selected(old('reporter_is_disability', '') === '')>— Tidak diisi —</option>
                <option value="0" @selected(old('reporter_is_disability') === '0')>Tidak</option>
                <option value="1" @selected(old('reporter_is_disability') === '1')>Ya</option>
              </select>
              @error('reporter_is_disability') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-slate-700">Pekerjaan Pelapor</label>
              <input
                type="text"
                name="reporter_job"
                value="{{ old('reporter_job') }}"
                placeholder="contoh: Pelajar/Mahasiswa, IRT, Buruh, Pegawai, dsb."
                class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
              />
              @error('reporter_job') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
          </div>
        </section>

        {{-- ============ ALAMAT / LOKASI ============ --}}
        <section>
          <h2 class="text-sm font-semibold text-slate-800">Lokasi/Alamat</h2>

          @if ($provinceTree->isEmpty())
            {{-- Fallback tanpa Laravolt --}}
            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-3">
              <div>
                <label class="block text-sm font-medium text-slate-700">Provinsi</label>
                <input
                  type="text"
                  name="province_name"
                  value="{{ old('province_name') }}"
                  class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
                />
                @error('province_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700">Kab/Kota</label>
                <input
                  type="text"
                  name="regency_name"
                  value="{{ old('regency_name') }}"
                  class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
                />
                @error('regency_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700">Kecamatan</label>
                <input
                  type="text"
                  name="district_name"
                  value="{{ old('district_name') }}"
                  class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
                />
                @error('district_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
              </div>
            </div>
          @else
            {{-- Dropdown berantai (codes + hidden names) --}}
            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-3">
              <div>
                <label class="block text-sm font-medium text-slate-700">Provinsi</label>
                <select
                  id="province_code"
                  name="province_code"
                  class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
                >
                  <option value="">— Pilih Provinsi —</option>
                </select>
                <input type="hidden" name="province_name" id="province_name" value="{{ old('province_name') }}">
                @error('province_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700">Kabupaten/Kota</label>
                <select
                  id="regency_code"
                  name="regency_code"
                  disabled
                  class="mt-1 w-full rounded-lg border-slate-300 disabled:bg-slate-50 focus:border-purple-500 focus:ring-purple-500"
                >
                  <option value="">— Pilih Kab/Kota —</option>
                </select>
                <input type="hidden" name="regency_name" id="regency_name" value="{{ old('regency_name') }}">
                @error('regency_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700">Kecamatan</label>
                <select
                  id="district_code"
                  name="district_code"
                  disabled
                  class="mt-1 w-full rounded-lg border-slate-300 disabled:bg-slate-50 focus:border-purple-500 focus:ring-purple-500"
                >
                  <option value="">— Pilih Kecamatan —</option>
                </select>
                <input type="hidden" name="district_name" id="district_name" value="{{ old('district_name') }}">
                @error('district_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
              </div>
            </div>
          @endif

          <div class="mt-3">
            <label class="block text-sm font-medium text-slate-700">Alamat Spesifik</label>
            <input
              type="text"
              name="reporter_address"
              value="{{ old('reporter_address') }}"
              placeholder="Jalan, No, RT/RW, Dusun/Kelurahan (bila perlu)"
              class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
            />
            @error('reporter_address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>
        </section>

        {{-- ============ KATEGORI KASUS ============ --}}
        <section>
          <h2 class="text-sm font-semibold text-slate-800">Kategori Kasus</h2>

          <div class="mt-3">
            <label class="block text-sm font-medium text-slate-700">Kategori (opsional)</label>
            <select
              name="category"
              class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
            >
              <option value="">— Pilih kategori —</option>
              @foreach ($categories as $opt)
                <option value="{{ $opt }}" @selected(old('category') === $opt)>{{ $opt }}</option>
              @endforeach
            </select>
            @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>
        </section>

        {{-- ============ DATA PELAKU (OPSIONAL) ============ --}}
        <section>
          <h2 class="text-sm font-semibold text-slate-800">Data Pelaku (Opsional)</h2>

          <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <label class="block text-sm font-medium text-slate-700">Nama Pelaku</label>
              <input
                type="text"
                name="perpetrator_name"
                value="{{ old('perpetrator_name') }}"
                class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
              />
              @error('perpetrator_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700">Umur Pelaku</label>
              <input
                type="number"
                name="perpetrator_age"
                min="0" max="120" step="1"
                value="{{ old('perpetrator_age') }}"
                class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
              />
              @error('perpetrator_age') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-slate-700">Pekerjaan Pelaku</label>
              <input
                type="text"
                name="perpetrator_job"
                value="{{ old('perpetrator_job') }}"
                class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
              />
              @error('perpetrator_job') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
          </div>
        </section>

        {{-- ============ DESKRIPSI ============ --}}
        <section>
          <h2 class="text-sm font-semibold text-slate-800">Rincian Peristiwa</h2>

          <div class="mt-3">
            <label class="block text-sm font-medium text-slate-700">Deskripsi</label>
            <textarea
              name="description"
              rows="6"
              required
              placeholder="Tuliskan kronologi singkat, waktu & tempat kejadian, kondisi saat ini, kebutuhan mendesak, dsb."
              class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
            >{{ old('description') }}</textarea>
            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>
        </section>

        {{-- ============ AKSI ============ --}}
        <div class="pt-2 flex items-center gap-3">
          <button class="rounded-lg bg-purple-700 px-4 py-2 font-semibold text-white hover:bg-purple-800">
            Kirim Pengaduan
          </button>
          <a
            href="{{ route('complaints.index') }}"
            class="rounded-lg border bg-white px-4 py-2 text-slate-800 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-purple-300"
          >
            Batal
          </a>
        </div>

        <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
          <p class="font-semibold">Catatan: Siapkan berkas saat datang ke kantor/asesmen</p>
          <ul class="mt-2 list-disc pl-5 space-y-1">
            <li>Fotokopi <strong>KTP</strong> pelapor/korban <span class="text-xs">(bawa asli untuk dicocokkan)</span></li>
            <li>Fotokopi <strong>Kartu Keluarga (KK)</strong></li>
            <li><strong>SKTM</strong> (bila diperlukan)</li>
            <li>Untuk kasus <strong>KDRT</strong>, bawa juga <strong>Buku Nikah</strong> / dokumen status perkawinan.</li>
          </ul>
        </div>

        <p class="text-xs text-slate-500">
          Data Anda kami jaga sesuai prinsip kerahasiaan & keselamatan. Kolom opsional boleh dikosongkan.
        </p>
      </form>
    </div>
  </div>

  {{-- === Data & Anchor untuk JS halaman=== --}}
  @if ($provinceTree->isNotEmpty())
    <script id="provinceTree" type="application/json">@json($provinceTree)</script>
  @endif

  <div
    id="complaints-create"
    data-old-province="{{ old('province_code') }}"
    data-old-regency="{{ old('regency_code') }}"
    data-old-district="{{ old('district_code') }}"
  ></div>
z
</x-app-layout>
