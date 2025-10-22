
<x-app-layout>
  {{-- Top menu --}}
  <x-slot name="header">
    @include('profile.partials.top-menu')
    </x-slot>

  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Tumpukan/stacked paper --}}
    <div class="relative">
      {{-- lembar bayangan 1 --}}
      <div
        class="pointer-events-none absolute -top-3 left-6 right-6 h-full rounded-2xl bg-slate-100 shadow-sm rotate-[-2deg]">
      </div>
      {{-- lembar bayangan 2 --}}
      <div
        class="pointer-events-none absolute -top-1 left-3 right-3 h-full rounded-2xl bg-slate-100 shadow-sm rotate-[1.5deg]">
      </div>

      {{-- Lembar utama --}}
      <article class="relative rounded-2xl bg-white border border-slate-200 shadow-sm mt-2">
        <header class="px-6 sm:px-8 pt-7 pb-4">
          <h1 class="text-3xl font-black tracking-tight text-slate-800">
            PERTANYAAN YANG SERING DIAJUKAN (FAQ)
          </h1>
          <div class="mt-3 h-0.5 w-28 bg-purple-600/80 rounded-full"></div>
        </header>

        <div class="px-6 sm:px-8 pb-8">
         <div class="space-y-6 text-slate-800 leading-relaxed">
            <h2 class="text-2xl font-semibold">1. Apa itu Sistem Pengaduan Masyarakat?</h2>
            <p>
              Sistem Pengaduan Masyarakat adalah platform yang memungkinkan warga untuk melaporkan pelanggaran atau masalah yang mereka hadapi secara aman dan rahasia.
            </p>

            <h2 class="text-2xl font-semibold">2. Bagaimana cara membuat laporan pengaduan?</h2>
            <p>
              Untuk membuat laporan pengaduan, Anda perlu mendaftar atau masuk ke akun Anda, kemudian mengisi formulir pengaduan dengan informasi yang lengkap dan menyertakan bukti pendukung jika ada.
            </p>

            <h2 class="text-2xl font-semibold">3. Apakah laporan saya akan dirahasiakan?</h2>
            <p>
              Ya, semua laporan yang Anda buat akan dijaga kerahasiaannya. Identitas Anda tidak akan diungkapkan tanpa izin Anda.
            </p>

            <h2 class="text-2xl font-semibold">4. Bagaimana saya bisa memantau status laporan saya?</h2>
            <p>
              Setelah membuat laporan, Anda dapat memantau statusnya melalui dashboard akun Anda di sistem ini.
            </p>

            <h2 class="text-2xl font-semibold">5. Siapa yang akan menindaklanjuti laporan saya?</h2>
            <p>
              Laporan Anda akan ditindaklanjuti oleh petugas yang berwenang sesuai dengan jenis pelanggaran yang dilaporkan.
            </p>
        </div>
        </div>
        
      </article>
      
    </div>
  
  </div>
  
</x-app-layout>
