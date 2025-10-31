@extends('errors.layout-hero')

@section('title', 'Kesalahan Server')
@section('heading', 'Ada yang tidak beres di server')
@section('message', 'Maaf, terjadi kesalahan pada sistem kami. Tim akan segera memeriksa.')
@section('cta', 'Kembali ke Beranda')

@section('art')
<svg viewBox="0 0 560 220" class="h-40 w-auto m-6" fill="none" role="img" aria-label="500">
  <defs><linearGradient id="vio500" x1="0" y1="0" x2="1" y2="1">
    <stop offset="0" stop-color="#7C3AED"/><stop offset="1" stop-color="#6D28D9"/></linearGradient></defs>
  <rect x="20" y="30" width="520" height="160" rx="24" fill="#EEE8FF"/>
  <g style="font: 800 96px/1 Inter, system-ui, 'Segoe UI', Roboto, Arial;">
    <text x="100" y="145" fill="url(#vio500)">5</text>
    <text x="240" y="145" fill="url(#vio500)">0</text>
    <text x="380" y="145" fill="url(#vio500)">0</text>
  </g>
</svg>
@endsection
