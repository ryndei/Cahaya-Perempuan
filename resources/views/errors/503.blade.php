@extends('errors.layout-hero')

@section('title', 'Sedang Pemeliharaan')
@section('heading', 'Layanan sedang pemeliharaan')
@section('message', 'Kami sedang melakukan pembaruan sistem agar layanan lebih baik. Silakan coba lagi nanti.')
@section('cta', 'Kembali ke Beranda')

@section('art')
<svg viewBox="0 0 560 220" class="h-40 w-auto m-6" fill="none" role="img" aria-label="503">
  <defs><linearGradient id="vio503" x1="0" y1="0" x2="1" y2="1">
    <stop offset="0" stop-color="#7C3AED"/><stop offset="1" stop-color="#6D28D9"/></linearGradient></defs>
  <rect x="20" y="30" width="520" height="160" rx="24" fill="#EEE8FF"/>
  <g style="font: 800 96px/1 Inter, system-ui, 'Segoe UI', Roboto, Arial;">
    <text x="100" y="145" fill="url(#vio503)">5</text>
    <text x="240" y="145" fill="url(#vio503)">0</text>
    <text x="380" y="145" fill="url(#vio503)">3</text>
  </g>
</svg>
@endsection
