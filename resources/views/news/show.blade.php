@extends('layouts.landing-header')

@section('title', $news->meta_title ?? $news->title)

@push('head')
  @php
    
    $desc = $news->meta_description ?: Str::limit(strip_tags($news->excerpt ?: $news->body), 160);
  @endphp
  <meta name="description" content="{{ $desc }}">
  <meta property="og:title" content="{{ $news->meta_title ?? $news->title }}">
  <meta property="og:description" content="{{ $desc }}">
  @if($news->cover_path)
    <meta property="og:image" content="{{ asset('storage/'.$news->cover_path) }}">
  @endif
  <meta property="og:type" content="article">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta name="twitter:card" content="summary_large_image">
  <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
@php
  $plain = trim(strip_tags($news->body ?? ''));
  $words = $plain ? str_word_count($plain) : 0;
  $readMinutes = max(1, (int) ceil($words / 200));
@endphp

<div class="bg-gradient-to-b from-purple-50/60 to-transparent">
  <div class="max-w-3xl mx-auto px-4 py-8 md:py-12">

    {{-- Back button (outline pill) --}}
    <div class="flex items-center justify-between gap-3">
      <a href="{{ route('news.index') }}"
         class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Berita
      </a>
    </div>

    {{-- Heading --}}
    <header class="mt-4">
      @if($news->category ?? null)
        <span class="inline-flex items-center rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-700">
          {{ $news->category }}
        </span>
      @endif

      <h1 class="mt-2 text-3xl md:text-4xl font-bold leading-tight text-slate-900">
        {{ $news->title }}
      </h1>

      <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-slate-500">
        <time datetime="{{ optional($news->published_at)->toDateString() }}">
          {{ $news->published_at?->translatedFormat('d M Y') }}
        </time>
      </div>
    </header>

    {{-- Cover --}}
    @if($news->cover_path)
      <figure class="mt-6 overflow-hidden rounded-2xl border bg-white">
        <img
          src="{{ asset('storage/'.$news->cover_path) }}"
          alt="{{ $news->title }}"
          class="h-64 w-full object-cover md:h-[22rem]"
          loading="lazy">
      </figure>
    @endif

    {{-- Body --}}
    <article class="prose prose-slate max-w-none mt-8
                    prose-headings:scroll-mt-24 prose-h2:mt-8 prose-h2:mb-3
                    prose-a:text-purple-700 hover:prose-a:text-purple-800">
      {!! $news->body !!}
    </article>

    {{-- Tags --}}
    @if(!empty($news->tags))
      <div class="mt-8 flex flex-wrap gap-2">
        @foreach((array)$news->tags as $tag)
          <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">#{{ $tag }}</span>
        @endforeach
      </div>
    @endif

  
    <div
      x-data="{
        copied:false,
        copy(){ navigator.clipboard.writeText('{{ url()->current() }}'); this.copied=true; setTimeout(()=>this.copied=false,1200); }
      }"
      class="mt-10 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      {{-- primary button (ungu pill seperti tombol lain) --}}
      <a href="{{ route('news.index') }}"
         class="inline-flex items-center justify-center rounded-full bg-purple-600 px-5 py-2.5 text-sm font-semibold text-white
                shadow-[0_8px_24px_rgba(109,40,217,0.25)] hover:bg-purple-700">
        Lihat Berita Lain
      </a>
    </div>

  </div>
</div>
@endsection

@section('title', $news->meta_title ?? $news->title)

@push('head')
  @php
    use Illuminate\Support\Str;
    $desc = $news->meta_description ?: Str::limit(strip_tags($news->excerpt ?: $news->body), 160);
  @endphp
  <meta name="description" content="{{ $desc }}">
  <meta property="og:title" content="{{ $news->meta_title ?? $news->title }}">
  <meta property="og:description" content="{{ $desc }}">
  @if($news->cover_path)
    <meta property="og:image" content="{{ asset('storage/'.$news->cover_path) }}">
  @endif
  <meta property="og:type" content="article">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta name="twitter:card" content="summary_large_image">
  <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
  @php
    $plain = trim(strip_tags($news->body ?? ''));
    $words = $plain ? str_word_count($plain) : 0;
    $readMinutes = max(1, (int) ceil($words / 200)); // ~200 kata/menit
  @endphp

  <div class="bg-gradient-to-b from-purple-50/60 to-transparent">
    <div class="max-w-3xl mx-auto px-4 py-8 md:py-12">
      <a href="{{ route('news.index') }}"
         class="inline-flex items-center gap-2 text-sm text-purple-700 hover:text-purple-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Berita
      </a>

      <header class="mt-3">
        @if($news->category ?? null)
          <span class="inline-flex items-center rounded-full bg-purple-100 text-purple-700 px-3 py-1 text-xs font-medium">
            {{ $news->category }}
          </span>
        @endif

        <h1 class="mt-2 text-3xl md:text-4xl font-bold leading-tight text-slate-900">
          {{ $news->title }}
        </h1>
      </header>

      @if($news->cover_path)
        <figure class="mt-6 overflow-hidden rounded-2xl border bg-white">
          <img src="{{ asset('storage/'.$news->cover_path) }}"
               alt="{{ $news->title }}"
               class="w-full h-64 md:h-[22rem] object-cover">
        </figure>
      @endif

      <article class="prose prose-slate max-w-none mt-8 prose-a:text-purple-700 hover:prose-a:text-purple-800">
        {!! $news->body !!}
      </article>

      @if(!empty($news->tags))
        <div class="mt-8 flex flex-wrap gap-2">
          @foreach((array)$news->tags as $tag)
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">#{{ $tag }}</span>
          @endforeach
        </div>
      @endif
        <a href="{{ route('news.index') }}" class="text-sm text-purple-700 hover:underline">
          Lihat berita lain →
        </a>
      </div>
    </div>
  </div>
@endsection
