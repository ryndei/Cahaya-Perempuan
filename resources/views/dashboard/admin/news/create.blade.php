<x-app-layout>
  <x-slot name="header">@include('profile.partials.top-menu-admin')</x-slot>
  <div class="max-w-4xl mx-auto px-4 py-6">
    <h1 class="text-lg font-semibold mb-4">Tambah Berita</h1>
    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.news.store') }}">
      @include('dashboard.admin.news._form', ['item'=>$item])
    </form>
  </div>
</x-app-layout>
