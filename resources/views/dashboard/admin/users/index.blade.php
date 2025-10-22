{{-- resources/views/dashboard/admin/users/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">@include('profile.partials.top-menu-admin')</x-slot>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold">Manajemen User</h1>
        <p class="text-sm text-slate-600">Hanya Super Admin yang dapat mengelola user & admin.</p>
      </div>
      <a href="{{ route('admin.users.create') }}"
         class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
        Tambah Pengguna
      </a>
    </div>

    @if (session('status'))
      <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        {{ session('status') }}
      </div>
    @endif

    {{-- Filter --}}
    <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-6 gap-3">
      <div class="md:col-span-3">
        <label class="block text-sm font-medium mb-1">Cari</label>
        <input type="text" name="q" value="{{ request('q') }}"
               class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
               placeholder="Nama atau email">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Role</label>
        <select name="role" class="w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
          <option value="all"   @selected(request('role','all')==='all')>Semua (User + Admin)</option>
          <option value="admin" @selected(request('role')==='admin')>Admin</option>
          <option value="user"  @selected(request('role')==='user')>User</option>
        </select>
      </div>
      <div class="md:col-span-2 flex items-end gap-2">
        <button class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Terapkan</button>
        <a href="{{ route('admin.users.index') }}" class="rounded-md border px-4 py-2 hover:bg-slate-50">Reset</a>
      </div>
    </form>

    {{-- =========================
         DESKTOP TABLE (>= md)
       ========================= --}}
    <div class="hidden md:block">
      <div class="rounded-lg border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 text-slate-700">
            <tr>
              <th class="px-4 py-3 text-left font-medium">Nama</th>
              <th class="px-4 py-3 text-left font-medium">Email</th>
              <th class="px-4 py-3 text-left font-medium">Role</th>
              <th class="px-4 py-3 text-left font-medium">Dibuat</th>
              <th class="px-4 py-3 text-left font-medium">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse ($users as $u)
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3">{{ $u->name }}</td>
                <td class="px-4 py-3">{{ $u->email }}</td>
                <td class="px-4 py-3">
                  @foreach ($u->getRoleNames() as $r)
                    <span class="mr-1 inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                      {{ $r }}
                    </span>
                  @endforeach
                </td>
                <td class="px-4 py-3 whitespace-nowrap">{{ optional($u->created_at)->format('d M Y H:i') }}</td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <a href="{{ route('admin.users.edit', $u) }}"
                       class="rounded-md border px-3 py-1.5 text-xs hover:bg-slate-50">Edit</a>

                    <form method="POST" action="{{ route('admin.users.resetPassword', $u) }}"
                          onsubmit="return confirm('Reset password untuk {{ $u->email }}?')">
                      @csrf
                      <button class="rounded-md bg-slate-800 text-white text-xs px-3 py-1.5 hover:bg-slate-900">
                        Reset Password
                      </button>
                    </form>

                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                          onsubmit="return confirm('Hapus user {{ $u->email }}?')">
                      @csrf @method('DELETE')
                      <button class="rounded-md bg-red-600 text-white text-xs px-3 py-1.5 hover:bg-red-700">
                        Hapus
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada user.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- =====================
         MOBILE CARDS (< md)
       ===================== --}}
    <div class="md:hidden space-y-3">
      @forelse ($users as $u)
        <div class="rounded-xl border border-slate-200 bg-white p-4">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-sm font-semibold text-slate-900">{{ $u->name }}</div>
              <div class="text-sm text-slate-700">{{ $u->email }}</div>
              <div class="mt-1 flex flex-wrap items-center gap-2">
                @foreach ($u->getRoleNames() as $r)
                  <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                    {{ $r }}
                  </span>
                @endforeach
                <span class="text-xs text-slate-500">• {{ optional($u->created_at)->format('d M Y H:i') }}</span>
              </div>
            </div>

            <a href="{{ route('admin.users.edit', $u) }}"
               class="shrink-0 rounded-md border px-3 py-1.5 text-xs hover:bg-slate-50">Edit</a>
          </div>

          <div class="mt-3 flex items-center gap-2">
            <form method="POST" class="inline" action="{{ route('admin.users.resetPassword', $u) }}"
                  onsubmit="return confirm('Reset password untuk {{ $u->email }}?')">
              @csrf
              <button class="rounded-md bg-slate-800 text-white text-xs px-3 py-1.5 hover:bg-slate-900">
                Reset Password
              </button>
            </form>

            <form method="POST" class="inline" action="{{ route('admin.users.destroy', $u) }}"
                  onsubmit="return confirm('Hapus user {{ $u->email }}?')">
              @csrf @method('DELETE')
              <button class="rounded-md bg-red-600 text-white text-xs px-3 py-1.5 hover:bg-red-700">
                Hapus
              </button>
            </form>
          </div>
        </div>
      @empty
        <div class="rounded-xl border border-slate-200 bg-white p-6 text-center text-slate-500">
          Belum ada user.
        </div>
      @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
      {{ $users->links() }}
    </div>
  </div>
</x-app-layout>
