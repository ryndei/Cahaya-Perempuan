
<x-app-layout>
  {{-- top-menu --}}

<x-slot name="header">
    @include('profile.partials.top-menu')
    </x-slot>

 
  <div class="max-w-5xl mx-auto p-6">
    <div class="rounded-2xl border border-slate-200 bg-white p-6">
      <p class="text-slate-700">Halaman pengaduan siap. Selanjutnya kita buat form & simpan ke database.</p>
      <a href="#"
         class="mt-4 inline-flex rounded-lg bg-purple-700 px-4 py-2 font-semibold text-white hover:bg-purple-800">
        Buat Pengaduan
      </a>
    </div>
  </div>
</x-app-layout>
