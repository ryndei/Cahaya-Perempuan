<x-app-layout>
  <x-slot name="header">@include('profile.partials.top-menu-admin')</x-slot>

  <div class="max-w-4xl mx-auto px-4 py-6">
    <h1 class="text-lg font-semibold mb-4">Edit Berita</h1>

    @if(session('success'))
      <div class="mb-3 rounded-lg bg-emerald-50 text-emerald-800 px-3 py-2 text-sm">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="mb-3 rounded-lg bg-red-50 text-red-700 px-3 py-2 text-sm">
        {{ session('error') }}
      </div>
    @endif

    <form method="POST" enctype="multipart/form-data"
          action="{{ route('admin.news.update', ['news' => $item]) }}">
      @csrf
      @method('PUT')
      @include('dashboard.admin.news._form', ['item' => $item])
    </form>
  </div>
</x-app-layout>
