{{-- resources/views/profile/partials/update-password-form.blade.php --}}
<form method="post" action="{{ route('password.update') }}" class="space-y-5">
  @csrf
  @method('put')

  <div>
    <x-input-label for="current_password" value="Kata Sandi Saat Ini" />
    <x-text-input id="current_password" name="current_password" type="password"
      class="mt-1 block w-full focus:border-purple-600 focus:ring-purple-600"
      autocomplete="current-password" />
    <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
  </div>

  <div>
    <x-input-label for="password" value="Kata Sandi Baru" />
    <x-text-input id="password" name="password" type="password"
      class="mt-1 block w-full focus:border-purple-600 focus:ring-purple-600"
      autocomplete="new-password" />
    <x-input-error :messages="$errors->get('password')" class="mt-2" />
  </div>

  <div>
    <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" />
    <x-text-input id="password_confirmation" name="password_confirmation" type="password"
      class="mt-1 block w-full focus:border-purple-600 focus:ring-purple-600"
      autocomplete="new-password" />
    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
  </div>

  <div class="flex items-center gap-3">
    <x-primary-button class="bg-purple-700 hover:bg-purple-800 focus:ring-purple-400/60">
      Perbarui Sandi
    </x-primary-button>

    @if (session('status') === 'password-updated')
      <p
        x-data="{ show: true }"
        x-show="show"
        x-transition
        x-init="setTimeout(() => show = false, 2500)"
        class="text-sm text-emerald-700">
        Sandi diperbarui.
      </p>
    @endif
  </div>
</form>
