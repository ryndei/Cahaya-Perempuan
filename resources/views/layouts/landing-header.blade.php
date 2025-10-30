<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', config('app.name', 'Support Center'))</title>

  {{-- tempat meta/tag tambahan dari child --}}
  @stack('meta')

  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  @else
    <script src="https://cdn.tailwindcss.com"></script>
  @endif
</head>
<body class="min-h-screen h-full bg-purple-50 text-slate-900 antialiased">
  {{-- HEADER --}}
  @include('profile.partials.landing-header')

  {{-- KONTEN HALAMAN (diisi oleh child) --}}
  <main class="min-h-screen px-4 py-10">
    @yield('content')
  </main>

  {{-- FOOTER --}}
  @include('profile.partials.landing-footer')

  {{-- tempat script tambahan dari child --}}
  @stack('scripts')
</body>
</html>
