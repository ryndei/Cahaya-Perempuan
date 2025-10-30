<x-app-layout>
  <x-slot name="header">@include('profile.partials.top-menu-admin')</x-slot>

  <div class="max-w-7xl mx-auto px-4 py-6">
    <div class="mb-4 flex items-center justify-between">
      <form method="GET" class="flex gap-2">
        <input name="q" value="{{ $q }}" placeholder="Cari judul/excerpt…" class="border rounded-lg px-3 py-2 text-sm">
        <button class="px-3 py-2 rounded-lg bg-purple-600 text-white text-sm">Cari</button>
      </form>
      <a href="{{ route('admin.news.create') }}" class="px-3 py-2 rounded-lg border text-sm hover:bg-slate-50">+ Tambah Berita</a>
    </div>

    <div class="rounded-2xl border bg-white overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 text-left">Judul</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Rilis</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($items as $it)
            <tr>
              <td class="px-4 py-3">
                <div class="font-medium">{{ $it->title }}</div>
                <div class="text-xs text-slate-500 truncate">{{ $it->excerpt }}</div>
              </td>
              <td class="px-4 py-3">
                <span class="px-2 py-1 rounded-full text-xs {{ $it->status==='published' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                  {{ ucfirst($it->status) }}
                </span>
              </td>
              <td class="px-4 py-3 text-slate-600">{{ $it->published_at?->format('d M Y H:i') ?? '—' }}</td>
              <td class="px-4 py-3 text-right">
                <a href="{{ route('admin.news.edit',$it) }}" class="px-3 py-1.5 rounded border text-xs hover:bg-slate-50">Edit</a>
                <form action="{{ route('admin.news.destroy',$it) }}" method="POST" class="inline"
                      onsubmit="return confirm('Hapus berita ini?')">
                  @csrf @method('DELETE')
                  <button class="px-3 py-1.5 rounded border text-xs text-red-600 hover:bg-red-50">Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="px-4 py-10 text-center text-slate-500">Belum ada berita.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">{{ $items->links() }}</div>
  </div>
</x-app-layout>
