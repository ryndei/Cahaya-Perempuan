<x-app-layout>
  <x-slot name="header">
    @include('profile.partials.top-menu')
  </x-slot>

  <div class="max-w-3xl mx-auto p-6">
    @if (session('status'))
      <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-green-700">
        {{ session('status') }}
      </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6">
      <h1 class="mb-4 text-lg font-semibold">Buat Pengaduan</h1>

      <form method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        {{-- Judul --}}
        <div>
          <label class="block text-sm font-medium text-slate-700">Judul</label>
          <input name="title" value="{{ old('title') }}" required
                 class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"/>
          @error('title')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
          
        </div>

        {{-- Kategori (select) --}}
        <div>
          <label class="block text-sm font-medium text-slate-700">Kategori (opsional)</label>
          <select name="category"
                  class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500">
            <option value="">— Pilih kategori —</option>
            @foreach(($categories ?? [
              'KDRT',
              'Pelecehan Seksual',
              'Kekerasan Seksual Berbasis Online (KSBO)',
              'Kekerasan dalam Pacaran',
              'Lainnya',
            ]) as $opt)
              <option value="{{ $opt }}" @selected(old('category')===$opt)>{{ $opt }}</option>
            @endforeach
          </select>
          @error('category')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- DATA PELAPOR (opsional) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700">Nama</label>
            <input name="reporter_name" value="{{ old('reporter_name') }}"
                   class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"/>
            @error('reporter_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">No. Telepon</label>
            <input name="reporter_phone" value="{{ old('reporter_phone') }}"
                   class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"
                   placeholder="08xxxxxxxxxx"/>
            @error('reporter_phone')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700">Alamat</label>
          <input name="reporter_address" value="{{ old('reporter_address') }}"
                 class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500"/>
          @error('reporter_address')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Deskripsi --}}
        <div>
          <label class="block text-sm font-medium text-slate-700">Deskripsi</label>
          <textarea name="description" rows="6" required
                    class="mt-1 w-full rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500">{{ old('description') }}</textarea>
          @error('description')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Lampiran --}}
        <div>
          <label class="block text-sm font-medium text-slate-700">Lampiran (opsional)</label>
          <input type="file" name="attachment"
                 class="mt-1 w-full rounded-lg border-slate-300 file:mr-3 file:rounded-md file:border-0 file:bg-purple-600 file:px-3 file:py-2 file:text-white hover:file:bg-purple-700"/>
          <p class="mt-1 text-xs text-slate-500">jpg, png, pdf, doc, mp4 ≤ 5MB</p>
          @error('attachment')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="pt-2">
          <button class="rounded-lg bg-purple-700 px-4 py-2 font-semibold text-white hover:bg-purple-800">
            Kirim Pengaduan
          </button>
          <a href="{{ route('complaints.index') }}" class="ml-2 text-slate-600 hover:underline">Batal</a>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
