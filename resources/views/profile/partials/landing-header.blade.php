<header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <a href="{{ url('/') }}" class="no-underline inline-flex items-center gap-3 select-none group" aria-label="Cahaya Perempuan">
        <img src="{{ asset('asset/Logo.png') }}" alt="Logo Cahaya Perempuan"
             class="h-12 w-auto object-contain transition-transform group-hover:scale-[1.02]" loading="lazy" decoding="async">
        <span class="text-2xl font-extrabold leading-none">
          <span class="text-purple-900 group-hover:text-purple-700 transition-colors">Cahaya</span>
          <span class="text-amber-700 group-hover:text-amber-600 transition-colors">Perempuan</span>
        </span>
      </a>

      <nav class="hidden md:flex items-center gap-1">
        <a href="/"
           class="no-underline inline-flex items-center rounded-lg px-3 py-2 text-base font-semibold
                  text-slate-800 hover:text-purple-700 transition-colors
                  underline-offset-4 hover:underline decoration-2 decoration-purple-300
                  focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple-400/60">
          Beranda
        </a>
        <a href="{{ route('profil-lembaga') }}"
           class="no-underline inline-flex items-center rounded-lg px-3 py-2 text-base font-semibold
                  text-slate-800 hover:text-purple-700 transition-colors
                  underline-offset-4 hover:underline decoration-2 decoration-purple-300
                  focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple-400/60">
          Profil Lembaga
        </a>
        <a href="{{ route('kontak-kami') }}"
           class="no-underline inline-flex items-center rounded-lg px-3 py-2 text-base font-semibold
                  text-slate-800 hover:text-purple-700 transition-colors
                  underline-offset-4 hover:underline decoration-2 decoration-purple-300
                  focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple-400/60">
          Kontak Kami
        </a>
        <a href="{{ route('cara-melapor-landing') }}"
           class="no-underline inline-flex items-center rounded-lg px-3 py-2 text-base font-semibold
                  text-slate-800 hover:text-purple-700 transition-colors
                  underline-offset-4 hover:underline decoration-2 decoration-purple-300
                  focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple-400/60">
          Cara Melapor
        </a>
        <a href="{{ route('syarat-layanan') }}"
           class="no-underline inline-flex items-center rounded-lg px-3 py-2 text-base font-semibold
                  text-slate-800 hover:text-purple-700 transition-colors
                  underline-offset-4 hover:underline decoration-2 decoration-purple-300
                  focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple-400/60">
          Syarat Layanan
        </a>
      </nav>

      @if (Route::has('login'))
        <div class="hidden md:flex items-center gap-2">
          @auth
            <a href="{{ url('/dashboard') }}"
               class="inline-flex items-center px-3 py-2 rounded-md text-sm font-semibold text-white bg-purple-700 hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-400/60">
              Dashboard
            </a>
          @else
            <a href="{{ route('login') }}"
               class="no-underline inline-flex items-center px-3 py-2 rounded-md text-sm font-semibold text-slate-800 border border-slate-300 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-300">
              Login
            </a>
            @if (Route::has('register'))
              <a href="{{ route('register') }}"
                 class="no-underline inline-flex items-center px-3 py-2 rounded-md text-sm font-semibold text-white bg-purple-700 hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-400/60">
                 Register
              </a>
            @endif
          @endauth
        </div>
      @endif

      <button type="button"
              class="md:hidden inline-flex items-center justify-center h-10 w-10 rounded-md border border-slate-200 text-slate-700"
              aria-label="Toggle Navigation"
              onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>

    @include('profile.partials.menu-mobile')
  </div>
</header>
