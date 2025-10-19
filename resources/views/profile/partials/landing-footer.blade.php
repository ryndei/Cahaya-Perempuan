<footer class="bg-white border-t border-slate-200">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid gap-8 md:grid-cols-3">
      <div>
        <h4 class="font-semibold text-slate-900">Cahaya Perempuan</h4>
        <p class="mt-2 text-sm text-slate-700">
          Berperspektif korban, rahasia, dan aman. Konseling, pendampingan, bantuan hukum, dan rumah perlindungan untuk penyintas di Bengkulu.
        </p>
      </div>
      <div>
        <h4 class="font-semibold text-slate-900">Tautan Cepat</h4>
        <ul class="mt-2 space-y-2 text-sm">
          <li><a class="no-underline hover:underline hover:text-purple-700" href="{{ url('/privacy') }}">Kebijakan Privasi</a></li>
          <li><a class="no-underline hover:underline hover:text-purple-700" href="{{ url('/terms') }}">Syarat Layanan</a></li>
          <li><a class="no-underline hover:underline hover:text-purple-700" href="{{ route('profil-lembaga') }}">Profil Lembaga</a></li>
          <li><a class="no-underline hover:underline hover:text-purple-700" href="{{ route('kontak-kami') }}">Kontak Kami</a></li>
        </ul>
      </div>

      <div>
        <h4 class="font-semibold text-slate-900">Connect With Us</h4>
        <div class="mt-3 flex items-center gap-3">
          {{-- WA --}}
          <a href="https://wa.me/6282306738686" target="_blank" rel="noopener"
             class="group h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center hover:bg-emerald-600 transition" aria-label="WhatsApp">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 text-slate-700 group-hover:text-white"><path fill="currentColor" d="M20.52 3.48A11.94 11.94 0 0 0 12.02 0C5.39 0 .02 5.37.02 12c0 2.11.55 4.17 1.6 6.01L0 24l6.16-1.6A12 12 0 0 0 12.02 24C18.65 24 24 18.63 24 12S18.65 0 12.02 0zM12 21.8c-1.88 0-3.72-.5-5.32-1.45l-.38-.23-3.75.97.99-3.65-.25-.41A9.8 9.8 0 0 1 2.2 12c0-5.4 4.4-9.8 9.8-9.8s9.8 4.4 9.8 9.8s-4.4 9.8-9.8 9.8z"/><path fill="currentColor" d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97c-.27-.1-.46-.15-.66.15c-.19.3-.76.97-.93 1.17c-.17.2-.34.22-.64.07c-.3-.15-1.27-.47-2.42-1.5c-.89-.79-1.49-1.77-1.66-2.07c-.17-.3 0-.46.13-.61c.13-.13.3-.34.45-.51c.15-.17.2-.29.3-.49c.1-.2.05-.37-.02-.52c-.07-.15-.66-1.6-.91-2.19c-.24-.58-.49-.5-.66-.5h-.56c-.2 0-.52.07-.79.37c-.27.3-1.04 1.02-1.04 2.49s1.06 2.88 1.21 3.08c.15.2 2.09 3.18 5.07 4.34c.71.29 1.27.46 1.71.59c.72.23 1.38.2 1.9.12c.58-.09 1.76-.72 2-1.41c.25-.7.25-1.3.17-1.42c-.08-.12-.28-.2-.58-.34z"/></svg>
          </a>
          {{-- IG --}}
          <a href="https://www.instagram.com/cahayaperempuanwcc/" target="_blank" rel="noopener"
             class="group h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center hover:bg-pink-600 transition" aria-label="Instagram">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 text-slate-700 group-hover:text-white"><path fill="currentColor" d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3zm5 3.5A4.5 4.5 0 1 1 7.5 12A4.51 4.51 0 0 1 12 7.5m0 2A2.5 2.5 0 1 0 14.5 12A2.5 2.5 0 0 0 12 9.5M18 6.4a1.1 1.1 0 1 1-1.1 1.1A1.1 1.1 0 0 1 18 6.4"/></svg>
          </a>
          {{-- FB --}}
          <a href="https://facebook.com/yourpage" target="_blank" rel="noopener"
             class="group h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center hover:bg-blue-600 transition" aria-label="Facebook">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 text-slate-700 group-hover:text-white"><path fill="currentColor" d="M13 10h3l-.4 3H13v9h-3v-9H8v-3h2V8.5A4 4 0 0 1 14.3 4H17v3h-2c-1 0-2 .5-2 1.7z"/></svg>
          </a>
          {{-- Email --}}
          <a href="mailto:cp.wccbengkulu@gmail.com?subject=Permohonan%20Informasi&body=Halo%20CP%20WCC,%0D%0A"
             class="group h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center hover:bg-purple-700 transition" aria-label="Email">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 text-slate-700 group-hover:text-white"><path fill="currentColor" d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2m0 4-8 5L4 8V6l8 5l8-5z"/></svg>
          </a>
        </div>
      </div>
    </div>
  </div>
</footer>
