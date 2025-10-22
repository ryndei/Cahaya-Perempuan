<footer class="mt-16 border-t border-slate-200 white:border-slate-800">
  <div class="mx-auto max-w-7xl px-4 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
    <span aria-hidden="true">©</span>
    <time datetime="{{ now()->format('Y') }}">{{ now()->year }}</time>
    <span class="mx-1">•</span>
    <span>{{ config('app.name', 'Support Center') }}</span>
    <span class="mx-1">—</span>
    <span>Berperspektif korban</span>
    <span class="mx-1" aria-hidden="true">•</span>
    <span>Rahasia</span>
    <span class="mx-1" aria-hidden="true">•</span>
    <span>Aman</span>
  </div>
</footer>
