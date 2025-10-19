<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'Support Center') }}</title>
  <meta name="description" content="Ruang aman dan rahasia bagi perempuan dan anak penyintas kekerasan berbasis gender.">
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=inter:400,500,600;figtree:400,600,700" rel="stylesheet" />
  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  @else
    <script src="https://cdn.tailwindcss.com"></script>
  @endif
</head>
<body class="min-h-screen h-full bg-purple-50 text-slate-900 antialiased">
  {{-- HEADER --}}
  @include('profile.partials.landing-header')

  {{-- PAGE CONTENT --}}
  <main>
    @yield('content')
  </main>

  {{-- FOOTER --}}
  @include('profile.partials.landing-footer')
</body>
</html>
