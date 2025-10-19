{{-- resources/views/profile/partials/delete-user-form.blade.php --}}
<section x-data="{ open:false }">
  <p class="text-sm text-slate-600">
    Setelah akun dihapus, semua resource & data Anda akan hilang. Tindakan ini tidak dapat dibatalkan.
  </p>

  <button
    type="button"
    @click="open = true"
    class="mt-4 inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400/60">
    Hapus Akun
  </button>

  {{-- Modal --}}
  <div x-show="open" x-transition
       class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/50 p-4">
    <div @click.away="open=false"
         class="w-full max-w-md rounded-2xl border border-slate-200 bg-white shadow-xl">
      <div class="border-b border-slate-100 px-5 py-4">
        <h3 class="text-base font-extrabold text-slate-900">Konfirmasi Penghapusan</h3>
        <p class="mt-1 text-sm text-slate-600">
          Masukkan kata sandi untuk mengonfirmasi penghapusan akun secara permanen.
        </p>
      </div>

      <form method="post" action="{{ route('profile.destroy') }}" class="px-5 py-5 space-y-4">
        @csrf
        @method('delete')

        <div>
          <x-input-label for="password" value="Kata Sandi" />
          <x-text-input id="password" name="password" type="password"
            class="mt-1 block w-full focus:border-purple-600 focus:ring-purple-600"
            placeholder="••••••••" />
          <x-input-error :messages="$errors->get('userDeletion')" class="mt-2" />
        </div>

        <div class="mt-2 flex items-center justify-end gap-3">
          <button type="button"
                  @click="open=false"
                  class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-100">
            Batal
          </button>
          <button type="submit"
                  class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400/60">
            Hapus Permanen
          </button>
        </div>
      </form>
    </div>
  </div>
</section>
