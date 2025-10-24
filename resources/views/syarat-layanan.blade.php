{{-- resources/views/policies/syarat-pelayanan.blade.php --}}
@extends('layouts.landing-header')
@section('content')
  <section class="bg-gradient-to-b from-purple-50 to-white py-10">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
      {{-- Judul --}}
      <header class="text-center">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Syarat Pelayanan</h1>
      </header>

      {{-- Ringkasan misi --}}
      <div class="mt-8 grid gap-6 sm:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <h2 class="text-lg font-bold text-slate-900">Ruang Aman & Nyaman</h2>
          <p class="mt-2 text-sm text-slate-700 leading-relaxed">
            {{ config('app.name') }} menyediakan <strong>konseling</strong>, <strong>pendampingan</strong>,
            <strong>bantuan hukum</strong>, dan <strong>rumah perlindungan</strong> yang berpihak pada korban serta
            menegakkan hak atas kebenaran, keadilan, dan pemulihan.
          </p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <h2 class="text-lg font-bold text-slate-900">Cakupan Kasus</h2>
          <p class="mt-2 text-sm text-slate-700 leading-relaxed">
            Layanan difokuskan pada kekerasan berbasis gender, antara lain: <em>KDRT</em>, kekerasan/pelengcehan seksual,
            kekerasan dalam pacaran, kekerasan berbasis gender online (KSBO), penelantaran, dan perdagangan orang.
          </p>
        </div>
      </div>

      {{-- Syarat Utama --}}
      <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-xl font-extrabold text-purple-800">1. Syarat Utama Penerima Layanan</h2>
        <ul class="mt-3 list-disc list-inside text-sm text-slate-800 space-y-2">
          <li><strong>Perempuan</strong> dari segala usia; dan/atau</li>
          <li><strong>Anak</strong> (di bawah 18 tahun), tanpa memandang status pernikahan.</li>
          <li>Mengalami, menyaksikan, atau terdampak <em>kekerasan berbasis gender</em>.</li>
        </ul>
        <p class="mt-3 text-sm text-slate-600">
          Catatan: Pengadu laki-laki yang membutuhkan bantuan akan <strong>dirujuk ke mitra layanan</strong> yang relevan sesuai mandat lembaga.
        </p>
      </div>

      {{-- Kriteria Tambahan & Prioritas --}}
      <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <h3 class="text-lg font-bold text-slate-900">2. Kriteria Tambahan</h3>
          <ul class="mt-3 list-disc list-inside text-sm text-slate-700 space-y-2">
            <li>Korban, saksi, atau pendamping keluarga inti/ wali dari korban perempuan/anak.</li>
            <li>Bersedia memberikan informasi dasar untuk asesmen awal (nama boleh samaran, kontak, ringkasan kejadian).</li>
            <li>Setuju terhadap prinsip layanan: aman, rahasia, berpihak pada korban, dan tanpa stigma.</li>
          </ul>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <h3 class="text-lg font-bold text-slate-900">3. Prioritas Penanganan</h3>
          <ul class="mt-3 list-disc list-inside text-sm text-slate-700 space-y-2">
            <li>Ancaman keselamatan/nyawa, situasi darurat medis, atau korban masih bersama pelaku.</li>
            <li>Korban <strong>anak</strong>, penyandang disabilitas, dan/atau sedang hamil.</li>
            <li>Kekerasan seksual yang baru terjadi (butuh rujukan medis forensik/kontrasepsi darurat).</li>
          </ul>
        </div>
      </div>

      {{-- Dokumen yang Dianjurkan (opsional) --}}
      <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6">
        <h3 class="text-lg font-bold text-slate-900">4. Dokumen yang Dianjurkan (Opsional)</h3>
        <p class="mt-1 text-sm text-slate-700">Tidak memiliki dokumen <em>tetap dilayani</em>. Jika ada, mohon siapkan:</p>
        <ul class="mt-2 list-disc list-inside text-sm text-slate-700 space-y-1">
          <li>Identitas (KTP/KK/akta lahir) korban/pendamping/wali.</li>
          <li>Bukti pendukung: rekam medis/visum, chat/screenshot, foto, video, saksi.</li>
          <li>Surat keterangan/penugasan dari lembaga asal (bila pengantar dari instansi).</li>
        </ul>
      </div>

      {{-- Alur Layanan --}}
      <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6">
        <h3 class="text-lg font-bold text-slate-900">5. Alur Layanan Singkat</h3>
        <ol class="mt-2 list-decimal list-inside text-sm text-slate-700 space-y-2">
          <li><strong>Kontak awal</strong> (WhatsApp/telepon/form pengaduan) & asesmen risiko.</li>
          <li><strong>Konseling awal</strong> & perencanaan keamanan (safety planning).</li>
          <li><strong>Pendampingan</strong>: medis, psikologis, hukum, sosial termasuk rujukan lintas sektor.</li>
          <li><strong>Rumah perlindungan</strong> (bila diperlukan & tersedia).</li>
          <li><strong>Pemulihan</strong> & pemantauan lanjutan.</li>
        </ol>
      </div>

      {{-- Kerahasiaan & Persetujuan --}}
      <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <h3 class="text-lg font-bold text-slate-900">6. Kerahasiaan & Persetujuan</h3>
          <ul class="mt-2 list-disc list-inside text-sm text-slate-700 space-y-2">
            <li>Data korban <strong>dirahasiakan</strong> dan digunakan untuk penanganan kasus.</li>
            <li>Berbagi data ke instansi rujukan hanya atas <strong>persetujuan</strong> korban/wali, kecuali diwajibkan hukum.</li>
            <li>Korban dapat meminta akses, koreksi, atau penghentian pendampingan kapan saja.</li>
          </ul>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <h3 class="text-lg font-bold text-slate-900">7. Ketentuan Lain</h3>
          <ul class="mt-2 list-disc list-inside text-sm text-slate-700 space-y-2">
            <li><strong>Gratis</strong> untuk layanan utama; biaya pihak ketiga (tes medis/forensik, transport) akan diinformasikan dulu jika ada.</li>
            <li>Larangan penyalahgunaan layanan (laporan palsu, intimidasi, penyebaran data).</li>
            <li>Kami berhak menolak/ menghentikan layanan jika di luar mandat (bukan perempuan/anak),
                membahayakan keselamatan, atau terdapat indikasi pelanggaran hukum oleh pelapor.</li>
          </ul>
        </div>
      </div>

      {{-- Jam Operasional & Darurat --}}
      <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6">
        <h3 class="text-lg font-bold text-slate-900">8. Akses Layanan</h3>
        <div class="mt-2 text-sm text-slate-700 space-y-2">
          <p><strong>Jam operasional:</strong> Senin–Jumat, 09.00–17.00 WIB (janji temu disarankan).</p>
          <p><strong>Darurat:</strong> Jika ada ancaman keselamatan jiwa, segera hubungi layanan darurat setempat.</p>
          <ul class="list-disc list-inside">
            <li>WhatsApp/Telepon: <a href="https://wa.me/6282306738686" class="text-purple-700 hover:underline">0823-0673-8686</a></li>
            <li>Email: <a href="mailto:cp.wccbengkulu@gmail.com" class="text-purple-700 hover:underline">cp.wccbengkulu@gmail.com</a></li>
            <li>Alamat: Jl. Indragiri 1 No. 03, Kota Bengkulu</li>
          </ul>
        </div>
      </div>

      {{-- CTA --}}
      <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
        <a href="{{ route('complaints.create') }}"
           class="inline-flex items-center rounded-xl bg-purple-700 px-5 py-3 text-white font-semibold hover:bg-purple-800">
          Ajukan Pengaduan
        </a>
        <a href="https://wa.me/6282306738686"
           class="inline-flex items-center rounded-xl border-2 border-purple-700 px-5 py-3 text-purple-700 font-semibold hover:bg-purple-50">
          Hubungi via WhatsApp
        </a>
      </div>

      <p class="mt-6 text-center text-xs text-slate-500">
        Dengan mengakses layanan, Anda menyatakan memahami dan menyetujui Syarat Pelayanan ini.
      </p>
    </div>
  </section>
  @endsection
