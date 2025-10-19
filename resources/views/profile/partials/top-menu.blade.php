{{-- resources/views/partials/top-menu.blade.php --}}

<nav class="mt-2 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
  @php
    $menu = [
      ['label'=>'Beranda','href'=>route('dashboard'),'icon'=>'M3 12l3.6-3.6a9 9 0 0112.8 0L23 12','active'=>request()->routeIs('dashboard')],
      ['label'=>'Pengaduan','href'=>route('pengaduan'),'icon'=>'M4 4h16v14H4z M8 22h8','active'=>request()->routeIs('pengaduan')],
      ['label'=>'Cara Melapor','href'=>route('cara-melapor'),'icon'=>'M4 6h16M4 12h16M4 18h10','active'=>request()->routeIs('cara-melapor')],
      ['label'=>'FAQ','href'=>route('FAQ'),'icon'=>'M12 18h.01 M8 9a4 4 0 118 0c0 2-3 2-3 4','active'=>request()->routeIs('FAQ')],
      ['label'=>'Hubungi Kami','href'=>route('kontak'),'icon'=>'M4 6l8 6 8-6v12H4z','active'=>request()->routeIs('kontak')],
    ];
  @endphp

  @foreach($menu as $m)
    <a href="{{ $m['href'] }}"
       class="group inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-semibold
              {{ $m['active'] ? 'border-purple-700 bg-purple-700 text-white' : 'border-slate-200 bg-white text-slate-800 hover:border-purple-300 hover:bg-purple-50' }}">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
           class="h-5 w-5 {{ $m['active'] ? 'text-white' : 'text-purple-700 group-hover:text-purple-800' }}"
           fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="{{ $m['icon'] }}"/>
      </svg>
      <span>{{ $m['label'] }}</span>
    </a>
  @endforeach
</nav>
