import './bootstrap';
import Alpine from 'alpinejs';


// Simple Tailwind toast. Global: window.toast({ message, type, duration })
function ensureRoot() {
  let root = document.getElementById('toast-root');
  if (!root) {
    root = document.createElement('div');
    root.id = 'toast-root';
    root.className = 'fixed top-4 right-4 z-[1000] space-y-2';
    document.body.appendChild(root);
  }
  return root;
}

function icon(type) {
  if (type === 'error') return 'M12 2a10 10 0 1 0 0 20...';         // (heroicon path shortened)
  if (type === 'info')  return 'M13 16h-1v-4h-1m1-4h.01...';
  return 'M12 2a10 10 0 1 0 0 20...'; // success
}

export function toast({ message, type = 'success', duration = 3500 } = {}) {
  const root = ensureRoot();
  const base =
    type === 'error' ? 'bg-rose-600'
    : type === 'info' ? 'bg-slate-800'
    : 'bg-emerald-600';

  const el = document.createElement('div');
  el.className = `pointer-events-auto rounded-xl shadow-lg ring-1 ring-black/5 px-4 py-3 text-sm text-white ${base}`;

  el.innerHTML = `
    <div class="flex items-start gap-3">
      <svg class="h-5 w-5 shrink-0 opacity-90" viewBox="0 0 24 24" fill="currentColor">
        <path d="${icon(type)}"></path>
      </svg>
      <div class="pr-2">${String(message || '').replace(/</g,'&lt;').replace(/>/g,'&gt;')}</div>
      <button type="button" class="ml-auto rounded px-1.5 opacity-80 hover:opacity-100">✕</button>
    </div>
  `;

  const close = () => {
    el.classList.add('animate-[fade-out_200ms_ease-out_forwards]');
    setTimeout(() => el.remove(), 180);
  };
  el.querySelector('button').addEventListener('click', close);

  root.appendChild(el);
  if (duration > 0) setTimeout(close, duration);
}

window.toast = toast; // global helper
