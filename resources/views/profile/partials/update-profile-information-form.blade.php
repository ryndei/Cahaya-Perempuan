
@php
  $user = auth()->user();
@endphp

<form method="post" action="{{ route('profile.update') }}" class="space-y-5">
  @csrf
  @method('patch')

  {{-- Nama --}}
  <div>
    <x-input-label for="name" value="Nama" />
    <x-text-input id="name" name="name" type="text"
      class="mt-1 block w-full focus:border-purple-600 focus:ring-purple-600"
      :value="old('name', $user->name)" required autocomplete="name" />
    <x-input-error class="mt-2" :messages="$errors->get('name')" />
  </div>

  {{-- Email --}}
  <div>
    <x-input-label for="email" value="Email" />
    <x-text-input id="email" name="email" type="email"
      class="mt-1 block w-full focus:border-purple-600 focus:ring-purple-600"
      :value="old('email', $user->email)" required autocomplete="username" />
    <x-input-error class="mt-2" :messages="$errors->get('email')" />

    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
      <div class="mt-3 rounded-lg bg-amber-50 border border-amber-200 p-3 text-sm text-amber-800">
        Email Anda belum terverifikasi.
        <a form="send-verification" href="{{ route('verification.notice') }}" class="ml-2 underline font-semibold hover:text-amber-900">
          Verifikasi Sekarang
        </a>
      </div>
    @endif
  </div>

  {{-- Submit --}}
  <div class="flex items-center gap-3">
    <x-primary-button class="bg-purple-700 hover:bg-purple-800 focus:ring-purple-400/60">
      Simpan Perubahan
    </x-primary-button>

    @if (session('status') === 'profile-updated')
      <p
        x-data="{ show: true }"
        x-show="show"
        x-transition
        x-init="setTimeout(() => show = false, 2500)"
        class="text-sm text-emerald-700">
        Tersimpan.
      </p>
    @endif
  </div>
</form>

{{-- form kirim ulang verifikasi --}}


