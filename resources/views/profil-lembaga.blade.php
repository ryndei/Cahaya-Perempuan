@extends('layouts.landing-header')

@section('content')

  {{-- Hero tipis --}}
  <section class="bg-gradient-to-b from-purple-50 to-white border-b border-slate-200/70">
    <div class="max-w-5xl mx-auto px-4 py-10 text-center">
      <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Profil Lembaga</h1>
      <p class="mt-3 text-slate-700 max-w-3xl mx-auto">
        Cahaya Perempuan adalah lembaga penyedia layanan bagi perempuan dan anak korban kekerasan,
        berperspektif korban serta menjunjung hak atas kebenaran, keadilan, dan pemulihan.
      </p>
    </div>
  </section>

  <div class="max-w-5xl mx-auto px-4 py-10 space-y-12">

    {{-- Sejarah --}}
    <section id="sejarah" class="scroll-mt-24">
      <h2 class="text-2xl font-extrabold text-purple-800">Sejarah</h2>
      <div class="mt-3 space-y-4 text-slate-700">
        <p>
          Cahaya Perempuan berdiri pada <strong>25 November 1999</strong> sebagai <em>Women’s Crisis Center (WCC)</em>,
          diprakarsai relawan PKBI Bengkulu dan Youth Centre Centra Citra Remaja Raflesia. Fokus awalnya berangkat dari
          layanan konseling remaja dan berkembang menjadi lembaga yang menangani kekerasan berbasis gender terhadap
          perempuan dan anak.
        </p>
        <p>
          Dalam perjalanannya (±25 tahun), penyesuaian nama dilakukan untuk selaras dengan dokumen resmi negara menjadi
          <strong>Cahaya Perempuan Bengkulu</strong>, tanpa mengubah cita-cita pendirian sebagai WCC: berpihak pada hak-hak
          korban, terutama hak atas kebenaran, keadilan, dan pemulihan.
        </p>
      </div>
    </section>

    {{-- Visi --}}
    <section id="visi" class="scroll-mt-24">
      <h2 class="text-2xl font-extrabold text-purple-800">Visi</h2>
      <div class="mt-3 rounded-xl border border-slate-200 bg-white p-5">
        <p class="text-slate-800">
          “Terwujudnya kekuatan masyarakat sipil untuk mendorong pemerintah yang bertanggung jawab
          dalam menghapuskan kekerasan terhadap perempuan dan anak perempuan—khususnya kekerasan seksual— 
          berlandaskan nilai-nilai keadilan dan kesetaraan gender, disabilitas, dan inklusif.”
        </p>
      </div>
    </section>

    {{-- Misi --}}
    <section id="misi" class="scroll-mt-24">
      <h2 class="text-2xl font-extrabold text-purple-800">Misi</h2>
      <ol class="mt-3 grid gap-3 sm:grid-cols-2 list-decimal list-inside text-slate-800">
        <li>Meningkatkan kapasitas masyarakat (terutama perempuan) dalam advokasi penghapusan kekerasan dan pemenuhan hak kesehatan seksual & reproduksi.</li>
        <li>Mengembangkan jaringan kerja advokasi penghapusan kekerasan dan pemenuhan hak kesehatan seksual & reproduksi.</li>
        <li>Menjadi pusat informasi dan pelayanan terpadu bagi perempuan dan anak perempuan korban kekerasan & kesehatan reproduksi.</li>
        <li>Memastikan pemerintah/pemangku kepentingan mengupayakan penghapusan kekerasan dan pemenuhan hak kesehatan seksual & reproduksi.</li>
        <li>Menguatkan kapasitas dan kemandirian organisasi.</li>
      </ol>
    </section>

    {{-- CTA layanan --}}
    <section class="rounded-2xl border border-slate-200 bg-gradient-to-br from-purple-50 to-amber-50 p-6">
      <h3 class="text-lg font-extrabold text-slate-900">Butuh Informasi atau Pendampingan?</h3>
      <p class="mt-1 text-slate-700">
        Kami menyediakan konseling, pendampingan, bantuan hukum, dan rumah perlindungan yang aman dan rahasia.
      </p>
      <div class="mt-4 flex flex-wrap gap-3">
        <a href="https://wa.me/6282306738686" target="_blank" rel="noopener"
           class="inline-flex items-center rounded-lg bg-purple-700 px-4 py-2 text-white font-semibold hover:bg-purple-800">
          Hubungi via WhatsApp
        </a>
        <a href="mailto:cp.wccbengkulu@gmail.com"
           class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 font-semibold text-slate-800 hover:bg-slate-100">
          Kirim Email
        </a>
      </div>
    </section>

  </div>
@endsection
