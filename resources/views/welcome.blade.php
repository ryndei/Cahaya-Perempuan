<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>{{ config('app.name', 'Support Center') }}</title>
  <meta name="description" content="Ruang aman dan nyaman bagi perempuan dan anak penyintas kekerasan berbasis gender.">

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=inter:400,500,600;figtree:400,600,700" rel="stylesheet" />

  {{-- Tailwind via Vite (atau CDN fallback) --}}
  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  @else
    <script src="https://cdn.tailwindcss.com"></script>
  @endif

  <style>
    /* Komponen kecil untuk separator — tidak perlu plugin Tailwind */
    .sep { position:relative; height:4.5rem }         /* h-18-ish */
    .sep svg { position:absolute; inset:auto 0 0 0; width:100%; height:100% }
    .no-motion * { animation:none !important; transition:none !important }
    @media (prefers-reduced-motion: reduce) { .no-motion * { animation:none !important; transition:none !important } }
  </style>
</head>

<body class="min-h-screen h-full bg-purple-50 text-slate-900 antialiased">
  {{-- HEADER --}}
  @include('profile.partials.landing-header')

  {{-- ================= HERO ================= --}}
  <section id="hero" class="relative overflow-hidden">
    <div class="absolute inset-0">
      <div class="absolute inset-0 bg-gradient-to-b from-purple-900/5 to-purple-900/10"></div>
      <img
        src="https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=1600&auto=format&fit=crop"
        alt="" class="w-full h-full object-cover opacity-30 select-none" />
    </div>

    <div class="relative z-10 mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-24 text-center">
      <h1 class="text-3xl md:text-5xl font-extrabold leading-tight">
        Ruang aman dan nyaman bagi perempuan dan anak
        yang mengalami kekerasan berbasis gender.
      </h1>

      <p class="mt-4 text-slate-700 max-w-3xl mx-auto">
        Kami menyediakan <strong>konseling</strong>, <strong>pendampingan</strong>, <strong>bantuan hukum</strong>, dan <strong>rumah perlindungan</strong>
        berpihak pada korban serta menegakkan hak atas kebenaran, keadilan, dan pemulihan.
      </p>

      <div class="mt-10 flex items-center justify-center gap-4">
        @guest
          @if (Route::has('login'))
            <a href="{{ route('login') }}"
               class="inline-flex items-center px-6 py-3 rounded-xl text-white bg-purple-700 hover:bg-purple-800 font-semibold shadow-sm">
               Login
            </a>
          @endif
          @if (Route::has('register'))
            <a href="{{ route('register') }}"
               class="inline-flex items-center px-6 py-3 rounded-xl border-2 border-purple-700 text-purple-700 hover:bg-purple-50 font-semibold">
               Register
            </a>
          @endif
        @else
          <a href="{{ url('/dashboard') }}"
             class="inline-flex items-center px-6 py-3 rounded-xl text-white bg-purple-700 hover:bg-purple-800 font-semibold shadow-sm">
             Go to Dashboard
          </a>
        @endguest
      </div>

      <p class="mt-6 text-sm text-slate-600">
        Pengguna baru? Daftar untuk akses dukungan yang <strong>aman</strong> dan <strong>rahasia</strong>.
      </p>
    </div>

    {{-- TRANSISI → ke SECTION BERITA (warna target: putih) --}}
    <div class="sep -mb-px">
      <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white"></div>
      <svg viewBox="0 0 1440 96" fill="currentColor" class="text-white" preserveAspectRatio="none">
        <path d="M0,64 C160,104 320,16 540,36 C760,56 900,104 1080,88 C1230,76 1320,34 1440,0 L1440,96 L0,96 Z"></path>
      </svg>
    </div>
  </section>

  {{-- ================= BERITA (bg putih) ================= --}}
  @php
    use Illuminate\Support\Facades\Cache;
    use App\Models\News;

    $homeNews = Cache::remember('home_news_3', 300, fn() =>
        News::published()->latest('published_at')->take(3)->get()
    );
  @endphp

  <section id="news" class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
      <div class="text-center">
        <p class="text-xs tracking-widest text-purple-700/70 font-semibold">PEMBARUAN</p>
        <h2 class="mt-1 text-2xl md:text-3xl font-bold">Berita Kegiatan</h2>
        <div class="mx-auto mt-3 h-1 w-20 rounded-full bg-amber-700/80"></div>
      </div>

      <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($homeNews as $n)
          <a href="{{ route('news.show',$n) }}"
             class="group rounded-2xl border bg-white overflow-hidden hover:shadow-lg transition">
            @if($n->cover_path)
              <img src="{{ asset('storage/'.$n->cover_path) }}" alt="{{ $n->title }}" class="h-40 w-full object-cover">
            @endif
            <div class="p-4">
              <div class="text-xs text-slate-500">{{ $n->published_at?->translatedFormat('d M Y') }}</div>
              <h3 class="mt-1 font-semibold group-hover:text-purple-700">{{ $n->title }}</h3>
              <p class="mt-1 text-sm text-slate-600 line-clamp-2">{{ $n->excerpt }}</p>
            </div>
          </a>
        @empty
          <div class="text-slate-500">Belum ada berita.</div>
        @endforelse
      </div>

      <div class="mt-8 text-center">
        <a href="{{ route('news.index') }}"
           class="inline-flex items-center rounded-lg border px-4 py-2 font-medium text-slate-800 hover:bg-slate-50">
          Lihat semua berita
        </a>
      </div>
    </div>
  </section>

  {{-- TRANSISI → ke LAYANAN (warna target: ungu tipis / purple-50) --}}
  <div class="sep -mt-px">
    <div class="absolute inset-0 bg-gradient-to-b from-white to-purple-50"></div>
    <svg viewBox="0 0 1440 96" fill="currentColor" class="text-purple-50" preserveAspectRatio="none">
      <path d="M0,0 L0,96 L1440,96 L1440,48 C1320,80 1200,80 1080,72 C840,56 720,0 540,16 C360,32 180,72 0,0 Z"></path>
    </svg>
  </div>

  {{-- ================= LAYANAN (bg purple-50 lembut) ================= --}}
  <section id="services" class="bg-purple-50/100">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-16">
      <header class="max-w-3xl mx-auto text-center">
        <p class="text-xs tracking-widest text-purple-700/70 font-semibold">LAYANAN</p>
        <h2 class="mt-1 text-3xl font-extrabold text-purple-800">Layanan Kami</h2>
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
          <p class="mt-2 text-sm text-slate-700">Ruang aman untuk bercerita dan memetakan kebutuhan agar penyintas memahami pilihan penyelesaian yang paling tepat.</p>
        </div>

        {{-- Card 2 --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow transition">
          <div class="h-16 w-16 rounded-xl bg-purple-100 flex items-center justify-center mb-4">
            <img src="{{ asset('asset/pendampingan.png') }}" alt="Ikon Pendampingan" class="h-8 w-8">
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Pendampingan</h3>
          <p class="mt-2 text-sm text-slate-700">Dukungan langsung selama proses pemulihan, termasuk rujukan/pendampingan ke fasilitas kesehatan, kepolisian, hingga persidangan.</p>
        </div>

        {{-- Card 3 --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow transition">
          <div class="h-16 w-16 rounded-xl bg-purple-100 flex items-center justify-center mb-4">
            <img src="{{ asset('asset/bantuan-hukum.png') }}" alt="Ikon Bantuan Hukum" class="h-8 w-8">
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Bantuan Hukum</h3>
          <p class="mt-2 text-sm text-slate-700">Akses bantuan hukum oleh advokat untuk memastikan hak atas perlindungan dan keadilan bagi penyintas.</p>
        </div>

        {{-- Card 4 --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow transition">
          <div class="h-16 w-16 rounded-xl bg-purple-100 flex items-center justify-center mb-4">
            <img src="{{ asset('asset/rumah-perlindungan.png') }}" alt="Ikon Rumah Perlindungan" class="h-8 w-8">
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Rumah Perlindungan</h3>
          <p class="mt-2 text-sm text-slate-700">Tempat aman sementara bagi perempuan dan anak yang membutuhkan perlindungan karena alasan keselamatan.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- FOOTER --}}
  @include('profile.partials.landing-footer')
</body>
</html>
