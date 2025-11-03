<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Assets -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="https://unpkg.com/alpinejs@3.x.x" defer></script>
</head>
<body class="font-sans antialiased">
  <div class="min-h-screen bg-gray-100 flex flex-col">
    @auth
      @include('layouts.navigation')
    @endauth

    @isset($header)
      <header class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          {{ $header }}
        </div>
      </header>
    @endisset

    <main id="page-main" class="flex-1">
      {{ $slot }}
    </main>

    <x-app-footer />
  </div>
  @include('sweetalert::alert')
  @stack('scripts')
</body>

</html>
