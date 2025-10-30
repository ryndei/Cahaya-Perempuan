@csrf
<div class="grid md:grid-cols-2 gap-4">
  <div>
    <label class="block text-sm mb-1">Judul</label>
    <input name="title" value="{{ old('title', $item->title) }}" class="w-full border rounded-lg px-3 py-2" required>
    @error('title')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
  </div>

  <div class="grid grid-cols-2 gap-2">
    <div>
      <label class="block text-sm mb-1">Status</label>
      <select name="status" class="w-full border rounded-lg px-3 py-2">
        <option value="draft" @selected(old('status', $item->status)==='draft')>Draft</option>
        <option value="published" @selected(old('status', $item->status)==='published')>Published</option>
      </select>
    </div>
    <div>
      <label class="block text-sm mb-1">Tanggal Rilis</label>
      <input type="datetime-local" name="published_at"
             value="{{ old('published_at', optional($item->published_at)->format('Y-m-d\TH:i')) }}"
             class="w-full border rounded-lg px-3 py-2">
    </div>
  </div>

  <div class="md:col-span-2">
    <label class="block text-sm mb-1">Excerpt (opsional)</label>
    <textarea name="excerpt" rows="2" class="w-full border rounded-lg px-3 py-2">{{ old('excerpt', $item->excerpt) }}</textarea>
  </div>

  <div class="md:col-span-2">
    <label class="block text-sm mb-1">Isi</label>
    <textarea name="body" rows="10" class="w-full border rounded-lg px-3 py-2" required>{{ old('body', $item->body) }}</textarea>
    @error('body')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm mb-1">Cover (jpg/png/webp, maks 2MB)</label>
    <input type="file" name="cover" accept="image/*" class="w-full">
    @if($item->cover_path)
      <img src="{{ asset('storage/'.$item->cover_path) }}" class="mt-2 h-24 rounded-lg object-cover">
    @endif
  </div>

  <div>
    <label class="block text-sm mb-1">Meta Title</label>
    <input name="meta_title" value="{{ old('meta_title', $item->meta_title) }}" class="w-full border rounded-lg px-3 py-2">
    <label class="block text-sm mt-2 mb-1">Meta Description</label>
    <input name="meta_description" value="{{ old('meta_description', $item->meta_description) }}" class="w-full border rounded-lg px-3 py-2">
  </div>
</div>

<div class="mt-4 flex items-center gap-2">
  <button class="px-4 py-2 rounded-lg bg-purple-600 text-white">Simpan</button>
  <a href="{{ route('admin.news.index') }}" class="px-4 py-2 rounded-lg border">Batal</a>
</div>
