@extends('layouts.landing-header')

@section('content')
  {{-- Hero tipis --}}
  <section class="bg-gradient-to-b from-purple-50 to-white border-b border-slate-200/70">
    <div class="max-w-6xl mx-auto px-4 py-10">
      <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Kontak Kami</h1>
      <p class="mt-2 text-slate-700">
        Informasi kontak Cahaya Perempuan silakan hubungi kami lewat kanal di bawah.
      </p>
    </div>
  </section>

  <div class="max-w-6xl mx-auto px-4 py-10 space-y-8">

    {{-- Baris: Alamat + Peta --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      {{-- Informasi alamat & kanal utama --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-xl font-extrabold text-slate-900">Alamat Kantor</h2>
        <address class="not-italic mt-2 text-slate-700 leading-relaxed">
          Jl. Indragiri 1 No. 03, Kel. Padang Harapan<br>
          Kota Bengkulu
        </address>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
          {{-- WhatsApp / Telepon --}}
          <a href="https://wa.me/6282306738686" target="_blank" rel="noopener"
             class="no-underline group rounded-xl border border-slate-200 p-4 hover:border-purple-300 hover:bg-purple-50 transition">
            <div class="flex items-start gap-3">
              <span class="grid h-10 w-10 place-items-center rounded-lg bg-purple-100 text-purple-700">☎️</span>
              <div>
                <div class="font-semibold text-slate-900 group-hover:text-purple-800">WhatsApp / Telepon</div>
                <div class="text-sm text-slate-600">0823-0673-8686</div>
              </div>
            </div>
          </a>

          {{-- Email --}}
          <a href="mailto:cp.wccbengkulu@gmail.com"
             class="no-underline group rounded-xl border border-slate-200 p-4 hover:border-purple-300 hover:bg-purple-50 transition">
            <div class="flex items-start gap-3">
              <span class="grid h-10 w-10 place-items-center rounded-lg bg-purple-100 text-purple-700">✉️</span>
              <div>
                <div class="font-semibold text-slate-900 group-hover:text-purple-800">Email</div>
                <div class="text-sm text-slate-600">cp.wccbengkulu@gmail.com</div>
              </div>
            </div>
          </a>
        </div>

        {{-- Tombol map --}}
        <div class="mt-4 flex flex-wrap gap-3">
          <a href="https://maps.google.com/?q=-3.8178125,102.2836875"
             target="_blank" rel="noopener"
             class="inline-flex items-center rounded-lg bg-purple-700 px-4 py-2 font-semibold text-white hover:bg-purple-800">
            Lihat di Google Maps
          </a>
          <a href="https://www.google.com/maps/dir/?api=1&destination=-3.8178125,102.2836875&travelmode=driving"
             target="_blank" rel="noopener"
             class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 font-semibold text-slate-800 hover:bg-slate-100">
            Dapatkan Rute
          </a>
        </div>
      </div>

      {{-- Peta --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-3">
        <div class="relative overflow-hidden rounded-xl shadow" style="padding-bottom:65%;">
          <iframe
            class="absolute inset-0 h-full w-full"
            src="https://www.google.com/maps?q=-3.8178125,102.2836875&z=15&output=embed"
            style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
            aria-label="Lokasi: 57JM+VF, Padang Harapan, Kota Bengkulu">
          </iframe>
        </div>
      </div>
    </div>

    {{-- Media Sosial --}}
    <section>
      <h2 class="text-xl font-extrabold text-slate-900">Media Sosial</h2>
      <p class="mt-1 text-slate-600">Ikuti kami untuk update kegiatan & layanan.</p>

      <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">

        <a href="https://wa.me/6282306738686" target="_blank" rel="noopener"
           class="no-underline group rounded-xl border border-slate-200 bg-white p-4 hover:border-emerald-300 hover:bg-emerald-50 transition">
          <div class="flex items-center gap-3">
            <span class="grid h-10 w-10 place-items-center rounded-full bg-emerald-100 text-emerald-700">WA</span>
            <span class="font-semibold text-slate-900 group-hover:text-emerald-700">WhatsApp</span>
          </div>
        </a>

        <a href="https://www.instagram.com/cahayaperempuanwcc/" target="_blank" rel="noopener"
           class="no-underline group rounded-xl border border-slate-200 bg-white p-4 hover:border-pink-300 hover:bg-pink-50 transition">
          <div class="flex items-center gap-3">
            <span class="grid h-10 w-10 place-items-center rounded-full bg-pink-100 text-pink-700">IG</span>
            <span class="font-semibold text-slate-900 group-hover:text-pink-700">Instagram</span>
          </div>
        </a>

        <a href="mailto:cp.wccbengkulu@gmail.com"
           class="no-underline group rounded-xl border border-slate-200 bg-white p-4 hover:border-purple-300 hover:bg-purple-50 transition">
          <div class="flex items-center gap-3">
            <span class="grid h-10 w-10 place-items-center rounded-full bg-purple-100 text-purple-700">✉️</span>
            <span class="font-semibold text-slate-900 group-hover:text-purple-700">Email</span>
          </div>
        </a>

        {{-- Tambah kanal lain bila ada --}}
      </div>
    </section>

    {{-- Catatan (opsional) --}}
    <p class="text-xs text-slate-500">
      *Untuk keadaan darurat yang mengancam keselamatan jiwa, segera hubungi layanan darurat setempat.
    </p>

  </div>
@endsection
