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
  <div class="max-w-4xl mx-auto px-4 py-8 md:py-12">

    {{-- Breadcrumb Navigation --}}
    <nav class="flex items-center space-x-2 text-sm text-slate-500 mb-6">
      <a href="/" class="hover:text-purple-600 transition-colors">Beranda</a>
      <span class="text-slate-300">›</span>
      <a href="{{ route('news.index') }}" class="hover:text-purple-600 transition-colors">Berita</a>
      <span class="text-slate-300">›</span>
      <span class="text-slate-700 truncate max-w-xs">{{ $news->title }}</span>
    </nav>

    {{-- Article Header --}}
    <header class="mb-8">
      @if($news->category ?? null)
        <div class="inline-flex items-center rounded-full bg-gradient-to-r from-purple-500 to-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-md mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
          </svg>
          {{ $news->category }}
        </div>
      @endif

      <h1 class="text-4xl md:text-5xl font-bold leading-tight text-slate-900 mb-4">
        {{ $news->title }}
      </h1>

      <div class="flex flex-wrap items-center gap-4 text-slate-600 border-b border-slate-200 pb-6">
        <div class="flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <time datetime="{{ optional($news->published_at)->toDateString() }}">
            {{ $news->published_at?->translatedFormat('d F Y') }}
          </time>
        </div>
        
        <div class="flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ $readMinutes }} menit membaca</span>
        </div>
        
        @if(!empty($news->author))
        <div class="flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <span>Oleh {{ $news->author }}</span>
        </div>
        @endif
      </div>
    </header>

    {{-- Cover Image --}}
    @if($news->cover_path)
      <figure class="mb-8 overflow-hidden rounded-xl shadow-lg transition-all duration-300 hover:shadow-xl">
        <img
          src="{{ asset('storage/'.$news->cover_path) }}"
          alt="{{ $news->title }}"
          class="w-full h-64 md:h-96 object-cover"
          loading="lazy">
      </figure>
    @endif

    {{-- Article Content --}}
    <article class="prose prose-lg max-w-none mb-10
                    prose-headings:scroll-mt-24 prose-h2:mt-10 prose-h2:mb-4 prose-h2:text-2xl
                    prose-h3:mt-8 prose-h3:mb-3 prose-h3:text-xl
                    prose-p:leading-relaxed prose-p:text-slate-700
                    prose-blockquote:border-l-purple-500 prose-blockquote:bg-purple-50/50 prose-blockquote:py-1 prose-blockquote:px-4 prose-blockquote:rounded-r
                    prose-strong:text-slate-900
                    prose-a:text-purple-700 hover:prose-a:text-purple-800 prose-a:font-medium prose-a:no-underline hover:prose-a:underline
                    prose-ul:list-disc prose-ol:list-decimal
                    prose-img:rounded-lg prose-img:shadow-md
                    prose-pre:bg-slate-800 prose-pre:text-slate-100">
      {!! $news->body !!}
    </article>

    {{-- Tags --}}
    @if(!empty($news->tags))
      <div class="mb-10">
        <h3 class="text-lg font-semibold text-slate-800 mb-3">Tagar:</h3>
        <div class="flex flex-wrap gap-2">
          @foreach((array)$news->tags as $tag)
            <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-purple-100 hover:text-purple-700 transition-colors cursor-pointer">
              #{{ $tag }}
            </span>
          @endforeach
        </div>
      </div>
    @endif

    {{-- Social Share & Navigation --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 pt-8 border-t border-slate-200">
      {{-- Social Sharing --}}
      <div class="flex flex-col">
        <span class="text-sm font-medium text-slate-700 mb-2">Bagikan berita ini:</span>
        <div class="flex space-x-2">
          <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
             target="_blank" 
             class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
          </a>
          <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($news->title) }}" 
             target="_blank" 
             class="flex items-center justify-center w-10 h-10 rounded-full bg-sky-500 text-white hover:bg-sky-600 transition-colors">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
            </svg>
          </a>
          <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" 
             target="_blank" 
             class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-700 text-white hover:bg-blue-800 transition-colors">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
            </svg>
          </a>
          <button 
            x-data="{ copied: false }"
            @click="navigator.clipboard.writeText('{{ url()->current() }}'); copied = true; setTimeout(() => copied = false, 2000)"
            class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-600 text-white hover:bg-slate-700 transition-colors relative">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            <span x-show="copied" class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-slate-800 text-white text-xs px-2 py-1 rounded-md whitespace-nowrap">
              Tersalin!
            </span>
          </button>
        </div>
      </div>

      {{-- Navigation Buttons --}}
      <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('news.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-full border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Kembali ke Berita
        </a>
        <a href="{{ route('news.index') }}"
           class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-purple-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_8px_24px_rgba(109,40,217,0.25)] hover:from-purple-700 hover:to-indigo-700 transition-all">
          Lihat Berita Lain
        </a>
      </div>
    </div>

    {{-- Related Articles (Optional) --}}
    {{-- 
    <div class="mt-12 pt-8 border-t border-slate-200">
      <h2 class="text-2xl font-bold text-slate-900 mb-6">Berita Terkait</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Related articles would go here -->
      </div>
    </div>
    --}}

  </div>
</div>
@endsection