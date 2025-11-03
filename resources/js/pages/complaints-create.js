// resources/js/pages/complaints-create.js
(function () {
  // Hanya jalan di halaman yang punya anchor ini
  const ROOT = document.getElementById('complaints-create');
  if (!ROOT) return;

  // Ambil JSON data dari tag <script type="application/json">
  const DATA_TAG = document.getElementById('provinceTree');
  /** @type {Array<{code:string,name:string,regencies:Array<{code:string,name:string,districts:Array<{code:string,name:string}>}>}>} */
  const DATA = DATA_TAG ? JSON.parse(DATA_TAG.textContent || '[]') : [];

  // Elemen
  const $prov = document.getElementById('province_code');
  const $kab  = document.getElementById('regency_code');
  const $kec  = document.getElementById('district_code');

  const $provName = document.getElementById('province_name');
  const $kabName  = document.getElementById('regency_name');
  const $kecName  = document.getElementById('district_name');

  // Old values dari Blade (data-attribute)
  const oldProv = ROOT.dataset.oldProvince || '';
  const oldKab  = ROOT.dataset.oldRegency  || '';
  const oldKec  = ROOT.dataset.oldDistrict || '';

  // Helpers
  function clearOptions(sel, placeholder) {
    sel.innerHTML = '';
    const opt = document.createElement('option');
    opt.value = '';
    opt.textContent = placeholder;
    sel.appendChild(opt);
  }

  function enable(sel, on) {
    sel.disabled = !on;
    sel.classList.toggle('bg-slate-50', !on);
  }

  function fillProvinces() {
    clearOptions($prov, '— Pilih Provinsi —');
    DATA.forEach(p => {
      const o = document.createElement('option');
      o.value = p.code;
      o.textContent = p.name;
      o.dataset.name = p.name;
      $prov.appendChild(o);
    });
  }

  function fillRegencies(provCode) {
    clearOptions($kab, '— Pilih Kab/Kota —');
    clearOptions($kec, '— Pilih Kecamatan —');
    enable($kab, false); enable($kec, false);

    const p = DATA.find(x => x.code === provCode);
    if (!p) return;

    p.regencies.forEach(r => {
      const o = document.createElement('option');
      o.value = r.code;
      o.textContent = r.name;
      o.dataset.name = r.name;
      $kab.appendChild(o);
    });
    enable($kab, true);
  }

  function fillDistricts(provCode, regCode) {
    clearOptions($kec, '— Pilih Kecamatan —');
    enable($kec, false);

    const p = DATA.find(x => x.code === provCode);
    if (!p) return;
    const r = p.regencies.find(x => x.code === regCode);
    if (!r) return;

    r.districts.forEach(d => {
      const o = document.createElement('option');
      o.value = d.code;
      o.textContent = d.name;
      o.dataset.name = d.name;
      $kec.appendChild(o);
    });
    enable($kec, true);
  }

  // Events
  $prov?.addEventListener('change', () => {
    const opt = $prov.selectedOptions[0];
    $provName.value = opt?.dataset.name || '';
    $kabName.value = ''; $kecName.value = '';
    fillRegencies($prov.value);
  });

  $kab?.addEventListener('change', () => {
    const opt = $kab.selectedOptions[0];
    $kabName.value = opt?.dataset.name || '';
    $kecName.value = '';
    fillDistricts($prov.value, $kab.value);
  });

  $kec?.addEventListener('change', () => {
    const opt = $kec.selectedOptions[0];
    $kecName.value = opt?.dataset.name || '';
  });

  // Init + restore
  fillProvinces();

  if (oldProv) {
    $prov.value = oldProv;
    $provName.value = $prov.selectedOptions[0]?.dataset.name || '';
    fillRegencies(oldProv);

    if (oldKab) {
      $kab.value = oldKab;
      $kabName.value = $kab.selectedOptions[0]?.dataset.name || '';
      fillDistricts(oldProv, oldKab);

      if (oldKec) {
        $kec.value = oldKec;
        $kecName.value = $kec.selectedOptions[0]?.dataset.name || '';
      }
    }
  }
})();
