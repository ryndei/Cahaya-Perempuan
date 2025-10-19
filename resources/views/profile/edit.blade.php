{{-- resources/views/profile/edit.blade.php --}}
<x-app-layout>
  {{-- Top menu --}}
  <x-slot name="header">
    @include('profile.partials.top-menu')
  </x-slot>

  {{-- Header section kecil --}}
  <div class="bg-gradient-to-r from-purple-50 to-amber-50 border-b border-slate-200">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
      <h1 class="text-2xl font-extrabold text-slate-900">Pengaturan Profil</h1>
      <p class="mt-1 text-sm text-slate-600">Kelola informasi akun, sandi, dan keamanan Anda.</p>
    </div>
  </div>

  <div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Kartu: Info Profil --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
          <header class="flex items-center justify-between border-b border-slate-100 px-5 sm:px-6 py-4">
            <div class="flex items-center gap-3">
              <span class="grid h-9 w-9 place-items-center rounded-full bg-purple-700 text-white text-sm font-bold">i</span>
              <div>
                <h2 class="text-base font-extrabold text-slate-900">Informasi Profil</h2>
                <p class="text-sm text-slate-600">Perbarui nama & email Anda.</p>
              </div>
            </div>
          </header>
          <div class="px-5 sm:px-6 py-6">
            @include('profile.partials.update-profile-information-form')
          </div>
        </section>

        {{-- Kartu: Ganti Password --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
          <header class="flex items-center justify-between border-b border-slate-100 px-5 sm:px-6 py-4">
            <div class="flex items-center gap-3">
              <span class="grid h-9 w-9 place-items-center rounded-full bg-amber-600 text-white text-sm font-bold">🔒</span>
              <div>
                <h2 class="text-base font-extrabold text-slate-900">Kata Sandi</h2>
                <p class="text-sm text-slate-600">Gunakan sandi yang kuat & unik.</p>
              </div>
            </div>
          </header>
          <div class="px-5 sm:px-6 py-6">
            @include('profile.partials.update-password-form')
          </div>
        </section>

        {{-- Kartu: Hapus Akun (full width) --}}
        <section class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm">
          <header class="flex items-center justify-between border-b border-slate-100 px-5 sm:px-6 py-4">
            <div class="flex items-center gap-3">
              <span class="grid h-9 w-9 place-items-center rounded-full bg-red-600 text-white text-sm font-bold">!</span>
              <div>
                <h2 class="text-base font-extrabold text-slate-900">Hapus Akun</h2>
                <p class="text-sm text-slate-600">Tindakan ini permanen — harap berhati-hati.</p>
              </div>
            </div>
          </header>
          <div class="px-5 sm:px-6 py-6">
            @include('profile.partials.delete-user-form')
          </div>
        </section>

      </div>
    </div>
  </div>
</x-app-layout>
