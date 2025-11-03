<nav class="mt-2 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">
  @php
    $menu = [
      [
        'label'  => 'Beranda',
        'href'   => route('admin.dashboard'),
        'icon'   => 'M3 12l3.6-3.6a9 9 0 0112.8 0L23 12',
        'active' => request()->routeIs('admin.dashboard'),
      ],
      [
        'label'  => 'Manajemen Pengaduan',
        'href'   => route('admin.complaints.index'),
        'icon'   => 'M4 4h16v14H4z M8 22h8',
        'active' => request()->routeIs('admin.complaints*'),
      ],
      [
        'label'  => 'Manajemen Kegiatan',
        'href'   => route('admin.news.index'),
        'icon'   => 'M4 4h16v14H4z M6 7h4v6H6z M12 7h6 M12 11h6 M12 15h6',
        'active' => request()->routeIs('admin.news*'),
      ],
    ];

    // Tambah menu "Manajemen User" hanya untuk super-admin
    if (auth()->check() && auth()->user()->hasRole('super-admin')) {
      $menu[] = [
        'label'  => 'Manajemen User',
        'href'   => route('admin.users.index'),
        // ikon “users”
        'icon'   => 'M16 11a4 4 0 10-8 0 4 4 0 008 0z M3 21a7 7 0 0118 0',
        'active' => request()->routeIs('admin.users*'),
      ];
    }

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
@include('sweetalert::alert')