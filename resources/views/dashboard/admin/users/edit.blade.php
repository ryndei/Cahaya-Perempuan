{{-- resources/views/dashboard/admin/users/edit.blade.php --}}
<x-app-layout>
  <x-slot name="header">@include('profile.partials.top-menu-admin')</x-slot>

  <div class="max-w-xl mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">Edit Pengguna</h1>

    @if (session('status'))
      <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        {{ session('status') }}
      </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
      @csrf @method('PATCH')

      <div>
        <label class="block text-sm font-medium">Nama</label>
        <input name="name" value="{{ old('name', $user->name) }}" class="mt-1 w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
        @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Email</label>
        <input name="email" type="email" value="{{ old('email', $user->email) }}" class="mt-1 w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
        @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Role</label>
        <select name="role" class="mt-1 w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
          <option value="admin" @selected(old('role', $user->hasRole('admin') ? 'admin' : '')==='admin')>Admin</option>
          <option value="user"  @selected(old('role', $user->hasRole('user')  ? 'user'  : '')==='user')>User</option>
        </select>
        @error('role') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="flex items-center gap-2">
        <button class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Simpan</button>
        <a href="{{ route('admin.users.index') }}" class="rounded-md border px-4 py-2 hover:bg-slate-50">Kembali</a>
      </div>
    </form>
  </div>
</x-app-layout>
