<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title') — {{ config('app.name', 'Cahaya Perempuan') }}</title>
  <meta name="robots" content="noindex, nofollow">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    @keyframes bob404 { 0%{transform:translateY(0)} 50%{transform:translateY(-10px)} 100%{transform:translateY(0)} }
    @keyframes fadeIn404 { from{opacity:0; transform:translateY(6px)} to{opacity:1; transform:translateY(0)} }
    .animate-bob404 { animation: bob404 3.2s ease-in-out infinite }
    .animate-fadeIn404 { animation: fadeIn404 .6s ease both }
    @media (prefers-reduced-motion: reduce){ .animate-bob404,.animate-fadeIn404{ animation:none!important } }
  </style>
</head>
<body class="h-full">
  <div class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-violet-50 to-slate-100 px-4">
    <main role="main" class="w-full max-w-5xl text-center animate-fadeIn404">
      <div class="mx-auto inline-block rounded-2xl bg-white/90 shadow-xl ring-1 ring-slate-200 backdrop-blur mb-8
                  transform-gpu will-change-transform animate-bob404">
        @yield('art')
      </div>

      <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold text-violet-700 tracking-tight">
        @yield('heading')
      </h1>
      <p class="mt-3 text-base sm:text-xl text-slate-700">
        @yield('message')
      </p>

      <div class="mt-8">
        <a href="{{ url('/') }}"
           class="inline-block rounded-full px-8 py-3 text-lg font-semibold text-white shadow-lg
                  bg-violet-600 hover:bg-violet-700 transition-transform hover:scale-105
                  focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-violet-600">
          @yield('cta', 'Kembali ke Beranda')
        </a>
      </div>

      @hasSection('extra')
        <div class="mt-6 text-sm text-slate-600">@yield('extra')</div>
      @endif
    </main>
  </div>
</body>
</html>
