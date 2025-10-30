@csrf

<div class="grid lg:grid-cols-3 gap-6">

  {{-- =================== Kolom Utama =================== --}}
  <div class="lg:col-span-2 space-y-6">

    {{-- Card: Judul & Excerpt --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <h3 class="text-sm font-semibold text-slate-700">Informasi Utama</h3>
      <div class="mt-4 space-y-4">

        {{-- Judul --}}
        <div>
          <label for="title" class="block text-sm text-slate-700 mb-1">Judul Kegiatan</label>
          <input
            id="title"
            name="title"
            value="{{ old('title', $item->title) }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500"
            maxlength="255" required
          >
          <div class="mt-1 flex justify-between text-xs">
            @error('title')
              <span class="text-rose-600">{{ $message }}</span>
            @else
              <span class="text-slate-400">Maks. 255 karakter</span>
            @enderror
            <span id="titleCount" class="text-slate-400">0/255</span>
          </div>
        </div>

        {{-- Excerpt --}}
        <div>
          <label for="excerpt" class="block text-sm text-slate-700 mb-1">Deskripsi Singkat</label>
          <textarea
            id="excerpt"
            name="excerpt"
            rows="3"
            maxlength="300"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500"
          >{{ old('excerpt', $item->excerpt) }}</textarea>
          <div class="mt-1 flex justify-between text-xs">
            <span class="text-slate-400">Maks. 300 karakter. Ditampilkan sebagai ringkasan.</span>
            <span id="excerptCount" class="text-slate-400">0/300</span>
          </div>
        </div>

      </div>
    </div>

    {{-- Card: Isi --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <h3 class="text-sm font-semibold text-slate-700">Konten</h3>
      <div class="mt-4">
        <label for="body" class="sr-only">Isi</label>
        <textarea
          id="body"
          name="body"
          rows="14"
          class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm leading-6 focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500"
          required
        >{{ old('body', $item->body) }}</textarea>
        <div class="mt-1 text-xs">
          @error('body')
            <span class="text-rose-600">{{ $message }}</span>
          @else
            <span class="text-slate-400">Gunakan paragraf singkat agar mudah dibaca.</span>
          @enderror
        </div>
      </div>
    </div>

  </div>

  {{-- =================== Panel Samping =================== --}}
  <div class="space-y-6">

    {{-- Card: Publikasi --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <h3 class="text-sm font-semibold text-slate-700">Publikasi</h3>
      <div class="mt-4 grid grid-cols-1 gap-3">
        {{-- Status --}}
        <div>
          <label for="status" class="block text-sm text-slate-700 mb-1">Status</label>
          <select
            id="status"
            name="status"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500"
          >
            <option value="draft" @selected(old('status', $item->status)==='draft')>Draft</option>
            <option value="published" @selected(old('status', $item->status)==='published')>Published</option>
          </select>
        </div>

        {{-- Tanggal Rilis --}}
        <div>
          <label for="published_at" class="block text-sm text-slate-700 mb-1">Tanggal Rilis</label>
          <input
            id="published_at"
            type="datetime-local"
            name="published_at"
            value="{{ old('published_at', optional($item->published_at)->format('Y-m-d\TH:i')) }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500"
          >
          <p class="mt-1 text-xs text-slate-400">Kosongkan bila masih draft.</p>
        </div>
      </div>
    </div>

    {{-- Card: Cover --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <h3 class="text-sm font-semibold text-slate-700">Gambar Cover</h3>

      {{-- Dropzone sederhana --}}
      <label for="cover"
             class="mt-4 block cursor-pointer rounded-xl border-2 border-dashed border-slate-300 hover:border-purple-400/70 bg-slate-50/50 p-4 text-center">
        <div class="text-sm text-slate-600">
          <strong>Klik untuk memilih</strong> atau seret file ke sini
        </div>
        <div class="mt-1 text-xs text-slate-400">jpg/png/webp (disarankan ≤ 2000×2000), maks 2MB</div>
        <input id="cover" type="file" name="cover" accept="image/*" class="hidden">
      </label>

      {{-- Preview --}}
      <div class="mt-3 space-y-2" id="coverPreviewWrap" style="display: none;">
        <img id="coverPreview" class="w-full h-40 object-cover rounded-lg border" alt="Preview cover">
        <div class="flex gap-2">
          <button type="button" id="btnChangeCover"
                  class="px-3 py-1.5 text-xs rounded-md border hover:bg-slate-50">Ganti</button>
          <button type="button" id="btnClearCover"
                  class="px-3 py-1.5 text-xs rounded-md bg-rose-600 text-white hover:bg-rose-700">Hapus</button>
        </div>
      </div>

      {{-- Preview jika sudah ada di server --}}
      @if($item->cover_path)
        <div class="mt-3">
          <div class="text-xs text-slate-500 mb-1">Cover saat ini:</div>
          <img src="{{ asset('storage/'.$item->cover_path) }}" class="w-full h-40 object-cover rounded-lg border">
        </div>
      @endif

      @error('cover') <div class="mt-2 text-xs text-rose-600">{{ $message }}</div> @enderror
    </div>

    {{-- Card: SEO --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <h3 class="text-sm font-semibold text-slate-700">SEO (Opsional)</h3>
      <div class="mt-4 space-y-4">
        <div>
          <label for="meta_title" class="block text-sm text-slate-700 mb-1">Meta Title</label>
          <input
            id="meta_title"
            name="meta_title"
            value="{{ old('meta_title', $item->meta_title) }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500"
            maxlength="255"
          >
          <div class="mt-1 flex justify-between text-xs">
            <span class="text-slate-400">Maks. 255 karakter</span>
            <span id="metaTitleCount" class="text-slate-400">0/255</span>
          </div>
        </div>

        <div>
          <label for="meta_description" class="block text-sm text-slate-700 mb-1">Meta Description</label>
          <input
            id="meta_description"
            name="meta_description"
            value="{{ old('meta_description', $item->meta_description) }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500"
            maxlength="160"
          >
          <div class="mt-1 flex justify-between text-xs">
            <span class="text-slate-400">Disarankan 140–160 karakter</span>
            <span id="metaDescCount" class="text-slate-400">0/160</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
      <div class="flex items-center gap-2">
        <button class="px-4 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700">
          Simpan
        </button>
        <a href="{{ route('admin.news.index') }}" class="px-4 py-2 rounded-lg border hover:bg-slate-50">
          Batal
        </a>
      </div>
    </div>

  </div>
</div>

{{-- ======== Helpers: Counter & Preview ======== --}}
<script>
  (function () {
    // Counters
    const counters = [
      { id: 'title', max: 255, out: 'titleCount' },
      { id: 'excerpt', max: 300, out: 'excerptCount' },
      { id: 'meta_title', max: 255, out: 'metaTitleCount' },
      { id: 'meta_description', max: 160, out: 'metaDescCount' },
    ];
    counters.forEach(c => {
      const i = document.getElementById(c.id);
      const o = document.getElementById(c.out);
      if (!i || !o) return;
      const upd = () => o.textContent = `${i.value.length}/${c.max}`;
      i.addEventListener('input', upd);
      upd();
    });

    // Cover preview
    const input = document.getElementById('cover');
    const wrap  = document.getElementById('coverPreviewWrap');
    const img   = document.getElementById('coverPreview');
    const btnChange = document.getElementById('btnChangeCover');
    const btnClear  = document.getElementById('btnClearCover');

    if (input && wrap && img) {
      const show = file => {
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
          img.src = e.target.result;
          wrap.style.display = 'block';
        };
        reader.readAsDataURL(file);
      };
      input.addEventListener('change', e => {
        const f = e.target.files?.[0];
        if (f) show(f);
      });
      btnChange?.addEventListener('click', () => input.click());
      btnClear?.addEventListener('click', () => {
        input.value = '';
        wrap.style.display = 'none';
        img.src = '';
      });
    }
  })();
</script>