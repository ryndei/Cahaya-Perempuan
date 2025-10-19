@props(['as' => 'a'])

<{{ $as }} {{ $attributes->merge([
    'class' => 'group flex items-center gap-3 px-3 py-2.5 text-sm font-semibold
                text-slate-700 hover:text-purple-800 hover:bg-purple-50
                transition-colors'
]) }}>
    {{ $slot }}
</{{ $as }}>
