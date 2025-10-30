@extends('layouts.landing-header')

@section('title', 'Berita Kegiatan')

@section('content')
  <div class="max-w-7xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold mb-6">Berita Kegiatan</h1>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($items as $n)
        <a href="{{ route('news.show', $n) }}"
           class="group rounded-2xl border bg-white overflow-hidden hover:shadow-lg transition">
          @if($n->cover_path)
            <img src="{{ asset('storage/'.$n->cover_path) }}" alt="{{ $n->title }}" class="h-40 w-full object-cover">
          @endif

          <div class="p-4">
            <div class="text-xs text-slate-500">
              {{ $n->published_at?->translatedFormat('d M Y') }}
            </div>
            <h3 class="mt-1 font-semibold group-hover:text-purple-700">
              {{ $n->title }}
            </h3>
            <p class="mt-1 text-sm text-slate-600 line-clamp-2">
              {{ $n->excerpt }}
            </p>
          </div>
        </a>
      @empty
        <div class="text-slate-500">Belum ada berita.</div>
      @endforelse
    </div>

    <div class="mt-6">
      {{ $items->links() }}
    </div>
  </div>
@endsection
