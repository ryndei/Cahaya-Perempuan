 <div id="mobileMenu" class="md:hidden hidden pb-4">
        <nav class="flex flex-col gap-2">
          <a href="/" class="no-underline px-3 py-2 rounded-md text-sm text-slate-800 hover:bg-purple-50 hover:text-purple-700">Beranda</a>
          <a href="{{ route('profil-lembaga') }}" class="no-underline px-3 py-2 rounded-md text-sm text-slate-800 hover:bg-purple-50 hover:text-purple-700">Profil Lembaga</a>
          <a href="{{ route('kontak-kami') }}" class="no-underline px-3 py-2 rounded-md text-sm text-slate-800 hover:bg-purple-50 hover:text-purple-700">Kontak Kami</a>
          <a href="{{ route('cara-melapor') }}" class="no-underline px-3 py-2 rounded-md text-sm text-slate-800 hover:bg-purple-50 hover:text-purple-700">Cara Melapor</a>
          <a href="{{ route('syarat-layanan') }}" class="no-underline px-3 py-2 rounded-md text-sm text-slate-800 hover:bg-purple-50 hover:text-purple-700">Syarat Layanan</a>

          @guest
            @if (Route::has('login'))
              <a href="{{ route('login') }}" class="no-underline px-3 py-2 rounded-md border border-slate-300 text-sm hover:bg-slate-50">Login</a>
            @endif
            @if (Route::has('register'))
              <a href="{{ route('register') }}" class="no-underline px-3 py-2 rounded-md text-sm font-semibold text-white bg-purple-700 hover:bg-purple-800">Register</a>
            @endif
          @else
            <a href="{{ url('/dashboard') }}" class="no-underline px-3 py-2 rounded-md text-sm font-semibold text-white bg-purple-700 hover:bg-purple-800">Dashboard</a>
          @endguest
        </nav>
      </div>