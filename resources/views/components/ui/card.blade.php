@props(['header'=>null])
<div {{ $attributes->merge(['class'=>'bg-white border border-slate-200 rounded-2xl shadow-sm']) }}>
  @if($header)
    <div class="px-4 py-3 border-b border-slate-200 font-semibold text-slate-900">{{ $header }}</div>
  @endif
  <div class="p-4">
    {{ $slot }}
  </div>
</div>
