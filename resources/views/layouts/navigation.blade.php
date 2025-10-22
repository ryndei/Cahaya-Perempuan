<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
  <!-- Primary Navigation Menu -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
      <div class="flex">
        <!-- Logo -->
        <div class="shrink-0 flex items-center">
          <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
          </a>
        </div>

        <!-- Left: Quick status / greeting (desktop) -->
        <div class="hidden sm:flex sm:items-center sm:ms-10">
          <div class="flex items-center gap-3 text-xs sm:text-sm">
            <span class="inline-flex items-center rounded-full bg-purple-100 text-purple-700 px-2.5 py-1 font-semibold">
              ● Online
            </span>
            <span class="hidden md:inline text-slate-500">
              Selamat datang, {{ Auth::user()->name }}
            </span>
          </div>
        </div>
      </div>

      <!-- Right: Settings Dropdown -->
      <div class="hidden sm:flex sm:items-center sm:ms-6">
        <x-dropdown align="right" width="56">
          <x-slot name="trigger">
            <button
              class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white
                     px-3 py-1.5 text-sm font-semibold text-slate-700 hover:bg-slate-50
                     focus:outline-none focus:ring-2 focus:ring-purple-300">
              {{-- Avatar inisial --}}
              <span class="grid h-7 w-7 place-items-center rounded-full bg-purple-700 text-white">
                {{ Str::upper(Str::substr(Auth::user()->name,0,1)) }}
              </span>
              <span class="hidden md:inline text-slate-700">
                {{ Auth::user()->name }}
              </span>
              <svg class="h-4 w-4 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                      d="M5.3 7.3a1 1 0 011.4 0L10 10.6l3.3-3.3a1 1 0 111.4 1.4l-4 4a1 1 0 01-1.4 0l-4-4a1 1 0 010-1.4z"
                      clip-rule="evenodd"/>
              </svg>
            </button>
          </x-slot>

          <x-slot name="content">
            {{-- Mini header --}}
            <div class="px-3 py-3 bg-gradient-to-r from-purple-50 to-amber-50 border-b border-slate-100">
              <div class="text-xs font-semibold text-slate-500">Masuk sebagai</div>
              <div class="text-sm font-extrabold text-slate-900 truncate">{{ Auth::user()->name }}</div>
              <div class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</div>
            </div>

            {{-- Items --}}
            <div class="py-1">
              {{-- Profile: tambah hover ungu agar konsisten --}}
              <x-dropdown-link :href="route('profile.edit')" class="hover:bg-purple-50">
                <svg class="h-4 w-4 text-purple-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0"/>
                </svg>
                Profile
              </x-dropdown-link>

              <div class="my-1 h-px bg-slate-100"></div>

              {{-- Logout: custom button full-width + hover ungu --}}
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="group block w-full px-4 py-2 text-start text-sm leading-5 text-slate-700
                               hover:bg-purple-50 focus:bg-purple-50 transition">
                  <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 text-purple-700 group-hover:text-purple-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15.75 9V4.5A1.5 1.5 0 0014.25 3h-6A1.5 1.5 0 006.75 4.5V19.5A1.5 1.5 0 008.25 21h6a1.5 1.5 0 001.5-1.5V15M12 9l3 3m0 0l-3 3m3-3H3"/>
                    </svg>
                    <span>Log Out</span>
                  </span>
                </button>
              </form>
            </div>
          </x-slot>
        </x-dropdown>
      </div>

      <!-- Hamburger (mobile) -->
      <div class="-me-2 flex items-center sm:hidden">
        <button @click="open = ! open"
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400
                       hover:text-gray-500 hover:bg-gray-100 focus:outline-none
                       focus:bg-gray-100 focus:text-gray-500 transition">
          <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Responsive Navigation Menu -->
  <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
    <!-- Responsive Settings Options -->
    <div class="pt-4 pb-1 border-t border-gray-200">
      <div class="px-4">
        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
      </div>

      <div class="mt-3 space-y-1">
        <x-responsive-nav-link :href="route('profile.edit')" class="hover:bg-purple-50">
          {{ __('Profile') }}
        </x-responsive-nav-link>

        <!-- Authentication -->
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <x-responsive-nav-link :href="route('logout')"
                                 class="hover:bg-purple-50"
                                 onclick="event.preventDefault(); this.closest('form').submit();">
            {{ __('Log Out') }}
          </x-responsive-nav-link>
        </form>
      </div>
    </div>
  </div>
</nav>
