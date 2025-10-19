@props(['title','value'])
<div {{ $attributes->merge(['class'=>'rounded-2xl border border-slate-200 bg-white p-4']) }}>
  <div class="text-xs text-slate-500">{{ $title }}</div>
  <div class="mt-1 text-2xl font-extrabold text-purple-800">{{ $value }}</div>
</div>
