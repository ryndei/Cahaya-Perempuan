{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'Support Center') }}</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Alpine.js (jika dipakai) -->
  <script src="https://unpkg.com/alpinejs@3.x.x" defer></script>
  <style>[x-cloak]{ display:none !important; }</style>
</head>
<body class="min-h-screen bg-purple-50 antialiased">
  

  <!-- Biarkan halaman anak yang mengatur kartu/brand -->
  <main class="min-h-screen flex items-center justify-center px-4 py-16">
    {{ $slot }}
  </main>
</body>
</html>
