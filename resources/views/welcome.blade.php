<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>{{ config('app.name', 'Support Center') }}</title>
  <meta name="description" content="Ruang aman dan rahasia bagi perempuan dan anak penyintas kekerasan berbasis gender.">

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=inter:400,500,600;figtree:400,600,700" rel="stylesheet" />

  {{-- Tailwind via Vite--}}
  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  @else
    {{-- Dev fallback: Tailwind CDN--}}
    <script src="https://cdn.tailwindcss.com"></script>
  @endif
</head>
<body class="min-h-screen h-full bg-purple-50 text-slate-900 antialiased">
{{-- header-navbar --}}
  @include('profile.partials.landing-header')

  {{-- HERO --}}
  <section class="relative">
    <div class="absolute inset-0 bg-gradient-to-b from-purple-900/5 to-purple-900/10">
      <img src="https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=1600&auto=format&fit=crop"
           alt="" class="w-full h-full object-cover opacity-30 select-none" />
    </div>

    <div class="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-24 text-center">
      <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900">
        Ruang aman dan rahasia bagi perempuan dan anak yang mengalami kekerasan berbasis gender.
      </h1>
      <p class="mt-4 text-slate-700 max-w-3xl mx-auto">
        Kami menyediakan <strong>konseling</strong>, <strong>pendampingan</strong>, <strong>bantuan hukum</strong>, dan <strong>rumah perlindungan</strong> berpihak pada korban serta menegakkan hak atas kebenaran, keadilan, dan pemulihan.
      </p>

      <div class="mt-10 flex items-center justify-center gap-4">
        @guest
          @if (Route::has('login'))
            <a href="{{ route('login') }}"
               class="no-underline inline-flex items-center px-6 py-3 rounded-xl text-white bg-purple-700 hover:bg-purple-800 font-semibold focus:outline-none focus:ring-2 focus:ring-purple-400/60">
               Login
            </a>
          @endif
          @if (Route::has('register'))
            <a href="{{ route('register') }}"
               class="no-underline inline-flex items-center px-6 py-3 rounded-xl border-2 border-purple-700 text-purple-700 hover:bg-purple-50 font-semibold focus:outline-none focus:ring-2 focus:ring-purple-300">
               Register
            </a>
          @endif
        @else
          <a href="{{ url('/dashboard') }}"
             class="inline-flex items-center px-6 py-3 rounded-xl text-white bg-purple-700 hover:bg-purple-800 font-semibold focus:outline-none focus:ring-2 focus:ring-purple-400/60">
             Go to Dashboard
          </a>
        @endguest
      </div>

      <p class="mt-6 text-sm text-slate-600">
        Pengguna baru? Daftar untuk akses dukungan yang <strong>aman</strong> dan <strong>rahasia</strong>.
      </p>
    </div>
  </section>

  {{-- OUR SERVICES --}}
  <section class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
      <header class="max-w-3xl mx-auto text-center">
        <h2 class="text-3xl font-extrabold text-purple-800">Layanan Kami</h2>
        <p class="mt-4 text-slate-700">
          Cahaya Perempuan WCC mendampingi penyintas dari pelaporan, pemulihan, hingga rujukan lintas sektor secara aman, rahasia, dan berpihak pada korban.
        </p>
        <div class="mt-4 h-1 w-16 bg-amber-700 rounded-full mx-auto"></div>
      </header>

      <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 gap-6">
        {{-- Card 1 --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow transition">
          <div class="h-16 w-16 rounded-xl bg-purple-100 flex items-center justify-center mb-4">
            <img src="{{ asset('asset/konseling.png') }}" alt="Ikon Konseling" class="h-8 w-8">
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Konseling</h3>
          <p class="mt-2 text-sm text-slate-700">
            Ruang aman untuk bercerita dan memetakan kebutuhan agar penyintas memahami pilihan penyelesaian yang paling tepat.
          </p>
        </div>

        {{-- Card 2 --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow transition">
          <div class="h-16 w-16 rounded-xl bg-purple-100 flex items-center justify-center mb-4">
            <img src="{{ asset('asset/pendampingan.png') }}" alt="Ikon Pendampingan" class="h-8 w-8">
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Pendampingan</h3>
          <p class="mt-2 text-sm text-slate-700">
            Dukungan langsung selama proses pemulihan, termasuk rujukan/pendampingan ke fasilitas kesehatan, kepolisian, hingga persidangan.
          </p>
        </div>

        {{-- Card 3 --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow transition">
          <div class="h-16 w-16 rounded-xl bg-purple-100 flex items-center justify-center mb-4">
            <img src="{{ asset('asset/bantuan-hukum.png') }}" alt="Ikon Bantuan Hukum" class="h-8 w-8">
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Bantuan Hukum</h3>
          <p class="mt-2 text-sm text-slate-700">
            Akses bantuan hukum oleh advokat untuk memastikan hak atas perlindungan dan keadilan bagi penyintas.
          </p>
        </div>

        {{-- Card 4 --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow transition">
          <div class="h-16 w-16 rounded-xl bg-purple-100 flex items-center justify-center mb-4">
            <img src="{{ asset('asset/rumah-perlindungan.png') }}" alt="Ikon Rumah Perlindungan" class="h-8 w-8">
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Rumah Perlindungan</h3>
          <p class="mt-2 text-sm text-slate-700">
            Tempat aman sementara bagi perempuan dan anak yang membutuhkan perlindungan karena alasan keselamatan.
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="py-14">
  <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
    <div class="rounded-3xl bg-white/60 backdrop-blur p-10 shadow-sm">
      <!-- Icon -->
      <div aria-hidden="true"
           class="mx-auto flex h-16 w-16 items-center justify-center rounded-full
                  bg-gradient-to-br from-purple-600 to-amber-600 text-2xl text-white">
        ☎️
      </div>

      <!-- Heading & Copy -->
      <h3 id="help-heading" class="mt-6 text-center text-2xl font-semibold text-slate-900">
        Butuh Bantuan Sekarang?
      </h3>
      <p class="mt-2 text-center text-slate-700">
        Jika Anda atau seseorang di sekitar Anda mengalami kekerasan, hubungi kami secara aman dan rahasia.
        Tim siap memberi informasi, konseling, dan rujukan layanan.
      </p>

      <!-- Kontak Utama -->
      <div class="mt-5 space-y-1 text-center">
        <p class="text-lg font-semibold text-amber-700">
          WhatsApp / Telepon:
          <a href="https://wa.me/6282306738686"
             class="underline hover:no-underline focus:outline-none focus:ring-2 focus:ring-purple-300">
            0823-0673-8686
          </a>
        </p>
        <p class="text-slate-700">
          Email:
          <a href="mailto:cp.wccbengkulu@gmail.com"
             class="underline hover:text-purple-700">
            cp.wccbengkulu@gmail.com
          </a>
        </p>
      </div>

      <!-- Peta -->
      <div class="mt-6 mx-auto w-full sm:max-w-md md:max-w-lg lg:max-w-xl">
  <div class="relative overflow-hidden rounded-xl shadow" style="padding-bottom:75%;">
    <iframe
      class="absolute inset-0 h-full w-full"
      src="https://www.google.com/maps?q=-3.8178125,102.2836875&z=15&output=embed"
      style="border:0;"
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
      aria-label="Lokasi: 57JM+VF, Padang Harapan, Kota Bengkulu">
    </iframe>
  </div>
</div>

      <!-- Tombol Aksi -->
      <div class="mt-4 flex flex-wrap items-center justify-center gap-3">
        <a href="https://maps.google.com/?q=-3.8178125,102.2836875"
           target="_blank" rel="noopener"
           class="inline-flex items-center rounded-lg bg-purple-700 px-4 py-2 font-semibold text-white hover:bg-purple-800">
          Lihat di Google Maps
        </a>
        <a href="https://www.google.com/maps/dir/?api=1&destination=-3.8178125,102.2836875&travelmode=driving"
           target="_blank" rel="noopener"
           class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 font-semibold text-slate-800 hover:bg-slate-100">
          Dapatkan Rute
        </a>
      </div>

      <!-- Alamat -->
      <address class="mt-4 not-italic text-center text-slate-700">
        <strong>Alamat Kantor:</strong><br>
        Jl. Indragiri 1 No. 03, Kel. Padang Harapan<br>
        Kota Bengkulu
      </address>

      <!-- Catatan -->
      <p class="mt-3 text-center text-xs text-slate-500">
        *Untuk keadaan darurat yang mengancam keselamatan jiwa, hubungi layanan darurat setempat.
      </p>
    </div>
  </div>
</section>
{{-- footer --}}
@include('profile.partials.landing-footer')
  
</body>
</html>
