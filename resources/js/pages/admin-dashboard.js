import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const $ = (id) => document.getElementById(id);
const make = (el, cfg) => el && new Chart(el, cfg);
const has = (arr) => Array.isArray(arr) && arr.length > 0;

document.addEventListener('DOMContentLoaded', () => {
  const dataEl = $('admin-dashboard-data');
  if (!dataEl) return;

  let p = {};
  try { p = JSON.parse(dataEl.textContent || '{}'); } catch { return; }

  // 1) Tren & distribusi
  if (p.timeseries?.labels && p.timeseries?.counts) {
    make($('chartTrend'), {
      type: 'line',
      data: { labels: p.timeseries.labels, datasets: [{ label: 'Pengaduan', data: p.timeseries.counts, tension: .35, fill: false }] },
      options: { plugins: { legend: { display: false }}, scales: { y: { beginAtZero: true }}}
    });
  }

  if (has(p.byStatus)) {
    make($('chartStatus'), {
      type: 'doughnut',
      data: { labels: p.byStatus.map(s=>s.label), datasets: [{ data: p.byStatus.map(s=>s.count) }] },
      options: { plugins: { legend: { position: 'bottom' } } }
    });
  }

  if (has(p.byCategory)) {
    make($('chartCategory'), {
      type: 'bar',
      data: { labels: p.byCategory.map(c=>c.category), datasets: [{ label: 'Jumlah', data: p.byCategory.map(c=>c.count) }] },
      options: { scales: { y: { beginAtZero: true }}}
    });
  }

  if (has(p.byProvince)) {
    make($('chartProvince'), {
      type: 'bar',
      data: { labels: p.byProvince.map(x=>x.province), datasets: [{ label: 'Jumlah', data: p.byProvince.map(x=>x.count) }] },
      options: { indexAxis: 'y', scales: { x: { beginAtZero: true }}}
    });
  }

  // 2) Umur (BUCKET default)
  if (has(p.ageBucketsReporter)) {
    make($('chartReporterAgesBucket'), {
      type: 'bar',
      data: { labels: p.ageBucketsReporter.map(a=>a.label), datasets: [{ label: 'Jumlah', data: p.ageBucketsReporter.map(a=>a.count) }] },
      options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 }}}}
    });
  }
  if (has(p.ageBucketsPerp)) {
    make($('chartPerpAgesBucket'), {
      type: 'bar',
      data: { labels: p.ageBucketsPerp.map(a=>a.label), datasets: [{ label: 'Jumlah', data: p.ageBucketsPerp.map(a=>a.count) }] },
      options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 }}}}
    });
  }

  // 3) Umur (DETAIL exact) – dirender saat ini juga, tapi dibungkus hidden; toggle hanya show/hide
  if (has(p.agesReporterExact.labels) && has(p.agesReporterExact.counts)) {
    make($('chartReporterAgesExact'), {
      type: 'bar',
      data: { labels: p.agesReporterExact.labels, datasets: [{ label: 'Jumlah', data: p.agesReporterExact.counts }] },
      options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 }}}, plugins: { legend: { display: false }}}
    });
  }
  if (has(p.agesPerpExact.labels) && has(p.agesPerpExact.counts)) {
    make($('chartPerpAgesExact'), {
      type: 'bar',
      data: { labels: p.agesPerpExact.labels, datasets: [{ label: 'Jumlah', data: p.agesPerpExact.counts }] },
      options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 }}}, plugins: { legend: { display: false }}}
    });
  }

  // Toggle detail umur
  const toggle = (btnId, wrapId) => {
    const b = $(btnId), w = $(wrapId);
    if (!b || !w) return;
    b.addEventListener('click', () => {
      const hidden = w.classList.toggle('hidden');
      b.textContent = hidden ? 'Lihat detail umur' : 'Sembunyikan detail';
    });
  };
  toggle('btnToggleRepAge', 'wrapRepExact');
  toggle('btnTogglePerpAge', 'wrapPerpExact');

  // 4) Top Pekerjaan
  if (has(p.jobsReporter)) {
    make($('chartJobsReporter'), {
      type: 'bar',
      data: { labels: p.jobsReporter.map(j=>j.label), datasets: [{ label: 'Jumlah', data: p.jobsReporter.map(j=>j.count) }] },
      options: { indexAxis: 'y', scales: { x: { beginAtZero: true, ticks: { precision: 0 }}}}
    });
  }
  if (has(p.jobsPerp)) {
    make($('chartJobsPerp'), {
      type: 'bar',
      data: { labels: p.jobsPerp.map(j=>j.label), datasets: [{ label: 'Jumlah', data: p.jobsPerp.map(j=>j.count) }] },
      options: { indexAxis: 'y', scales: { x: { beginAtZero: true, ticks: { precision: 0 }}}}
    });
  }
});
