/* NafasAman - index.js
   Beranda: cek udara, grafik gejala, peta titik api, daftar fasilitas. */
(function () {
  'use strict';
  const { esc, timeAgo } = NA;
  const $ = (sel) => document.querySelector(sel);

  const pill = (text, pressed, attrs, extra) =>
    '<button type="button" class="pill' + (extra ? ' ' + extra : '') + '" aria-pressed="' + pressed + '" ' + attrs + '>' + esc(text) + '</button>';

  /* ---------- 1. Cek udara ---------- */
  const air = { city: 'plk', group: 'umum', data: {} };

  function renderAirPills() {
    $('#city-pills').innerHTML = NA.CITIES.map((c) => pill(c.name, air.city === c.id, 'data-city="' + c.id + '"')).join('');
    $('#group-pills').innerHTML = NA.GROUPS.map((g) => pill(g.name, air.group === g.id, 'data-group="' + g.id + '"')).join('');
  }

  function renderAir() {
    const city = NA.CITIES.find((c) => c.id === air.city);
    const d = air.data[city.id] || { ispu: city.sample, live: false, pending: true };
    const key = NA.levelKey(d.ispu);
    const group = NA.GROUPS.find((g) => g.id === air.group);
    $('#air-result').className = 'air-result lv-' + key;
    $('#air-city').textContent = city.name;
    $('#air-ispu').textContent = d.ispu;
    $('#air-level').innerHTML = '<i class="dot"></i>' + esc(NA.LEVELS[key]);
    $('#air-tips').innerHTML = NA.ADVICE[key].concat([group.tip]).map((t) => '<li>' + esc(t) + '</li>').join('');
  }

  function loadAir() {
    NA.CITIES.forEach(async (c) => {
      air.data[c.id] = { ispu: c.sample, live: false, pending: true };
      try {
        const pm = await NA.fetchPm25(c);
        air.data[c.id] = { ispu: NA.pm25ToIspu(pm), live: true };
      } catch (e) {
        air.data[c.id] = { ispu: c.sample, live: false };
      }
      renderAir();
    });
  }

  $('#cek').addEventListener('click', (e) => {
    const b = e.target.closest('button');
    if (!b) return;
    if (b.dataset.city) air.city = b.dataset.city;
    if (b.dataset.group) air.group = b.dataset.group;
    renderAirPills();
    renderAir();
  });

  /* ---------- 2. Grafik gejala per wilayah ---------- */
  function renderHealth() {
    const rows = NA.health.byWilayah().slice(0, 5);
    const max = rows.length ? rows[0].n : 1;
    $('#bars').innerHTML = rows.length
      ? rows
          .map(
            (r) =>
              '<div class="bar-row"><span>' + esc(r.name) + '</span>' +
              '<div class="bar-track" aria-hidden="true"><div class="bar-fill" style="width:' + Math.round((r.n / max) * 100) + '%"></div></div>' +
              '<b>' + r.n + '</b></div>'
          )
          .join('')
      : '<p class="muted">Belum ada laporan gejala.</p>';
    $('#sym-top').innerHTML = NA.health
      .bySymptom()
      .slice(0, 3)
      .map((s) => '<span class="chip neutral">' + esc(NA.symLabel(s.id)) + ' &middot; ' + s.n + '</span>')
      .join('');
  }

  /* ---------- 3. Peta & daftar titik api ---------- */
  const fire = { filter: 'semua', sel: null, map: null, layer: null, markers: {} };
  const PHOTO_ICON =
    '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2.5" stroke="currentColor" stroke-width="1.8"></rect><circle cx="9" cy="10.5" r="1.8" stroke="currentColor" stroke-width="1.6"></circle><path d="M4 17l5-4.5 3.5 3L16 12l4 4" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"></path></svg>';

  const visibleReports = () =>
    NA.fire
      .all()
      .filter((r) => r.state === 'approved' && (fire.filter === 'semua' || r.st === fire.filter))
      .sort((a, b) => b.ts - a.ts);

  function renderFilters() {
    const items = [['semua', 'Semua'], ['api', 'Masih terbakar'], ['tangani', 'Dalam penanganan'], ['padam', 'Sudah padam']];
    $('#filter-pills').innerHTML = items.map(([k, l]) => pill(l, fire.filter === k, 'data-filter="' + k + '"', 'dark')).join('');
  }

  function pinIcon(r) {
    return L.divIcon({
      className: 'na-pin',
      html: '<span class="pin pin-' + r.st + (fire.sel === r.id ? ' is-selected' : '') + '"></span>',
      iconSize: [36, 36],
      iconAnchor: [18, 18]
    });
  }

  function renderMarkers() {
    if (!fire.map) return;
    fire.layer.clearLayers();
    fire.markers = {};
    visibleReports().forEach((r) => {
      const m = L.marker([r.lat, r.lng], { icon: pinIcon(r), title: r.place + ', ' + NA.STATUS[r.st], keyboard: true });
      m.bindPopup('<strong>' + esc(r.place) + '</strong><br>' + esc(NA.STATUS[r.st]) + ' &middot; ' + esc(timeAgo(r.ts)));
      m.on('click', () => select(r.id, false));
      m.addTo(fire.layer);
      fire.markers[r.id] = m;
    });
  }

  function renderList() {
    const list = visibleReports();
    const votes = NA.fire.votes();
    $('#reports').innerHTML = list.length
      ? list
          .map((r) => {
            const vy = !!votes[r.id + ':y'];  
            const vn = !!votes[r.id + ':n'];
            const isPhoto = r.photo && (r.photo.indexOf('data:image/') === 0 || r.photo.indexOf('assets/img/') !== -1);
            const photo = isPhoto
              ? '<img src="' + esc(r.photo) + '" alt="Foto laporan ' + esc(r.place) + '">'
              : PHOTO_ICON;
              
            return (
              '<article class="report' + (fire.sel === r.id ? ' is-selected' : '') + '" data-id="' + esc(r.id) + '">' +
              '<div class="report-photo">' + photo + '</div>' +
              '<div class="report-body">' +
              '<div class="report-meta"><span class="chip st-' + r.st + '"><i class="dot"></i>' + esc(NA.STATUS[r.st]) + '</span>' +
              '<span class="small muted">' + esc(timeAgo(r.ts)) + '</span></div>' +
              '<button type="button" class="report-title" data-act="focus">' + esc(r.place) + '</button>' +
              '<p class="small muted" style="margin:0">' + esc(r.desc) + '</p>' +
              '<div class="report-actions">' +
              '<button type="button" class="btn-sm" data-act="y" aria-pressed="' + vy + '"><span>Masih ada api</span><b>' + r.yes + '</b></button>' +
              '<button type="button" class="btn-sm" data-act="n" aria-pressed="' + vn + '"><span>Sudah padam</span><b>' + r.no + '</b></button>' +
              '</div></div></article>'
            );
          })
          .join('')
      : '<div class="empty">Belum ada laporan untuk filter ini.</div>';
  }

  function select(id, fly) {
    fire.sel = id;
    Object.keys(fire.markers).forEach((k) => {
      const r = NA.fire.all().find((x) => x.id === k);
      if (r) fire.markers[k].setIcon(pinIcon(r));
    });
    renderList();
    const m = fire.markers[id];
    if (fly && m && fire.map) {
      fire.map.flyTo(m.getLatLng(), Math.max(fire.map.getZoom(), 9));
      m.openPopup();
    }
  }

  function refreshFire() {
    renderMarkers();
    renderList();
  }

  function initMap() {
    const el = $('#map');
    if (typeof L === 'undefined') {
      el.innerHTML = '<div class="empty" style="margin:16px">Peta tidak bisa dimuat. Periksa koneksi internet (Leaflet dimuat dari CDN).</div>';
      return;
    }
    fire.map = L.map('map', { scrollWheelZoom: false }).setView([-2.2, 108.3], 5);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(fire.map);
    fire.layer = L.layerGroup().addTo(fire.map);
    fire.map.on('focus', () => fire.map.scrollWheelZoom.enable());
    fire.map.on('blur', () => fire.map.scrollWheelZoom.disable());
  }

  $('#filter-pills').addEventListener('click', (e) => {
    const b = e.target.closest('button[data-filter]');
    if (!b) return;
    fire.filter = b.dataset.filter;
    renderFilters();
    refreshFire();
  });

  $('#reports').addEventListener('click', (e) => {
    const b = e.target.closest('button[data-act]');
    const card = e.target.closest('.report');
    if (!b || !card) return;
    const id = card.dataset.id;
    if (b.dataset.act === 'focus') return select(id, true);
    // Konfirmasi warga: "masih ada api" (y) atau "sudah padam" (n). Klik lagi untuk membatalkan.
    const key = id + ':' + b.dataset.act;
    const votes = NA.fire.votes();
    const on = !votes[key];
    votes[key] = on;
    NA.fire.saveVotes(votes);
    NA.fire.update(id, (r) => {
      const f = b.dataset.act === 'y' ? 'yes' : 'no';
      r[f] = Math.max(0, r[f] + (on ? 1 : -1));
    });
    renderList();
  });

  /* ---------- 4. Fasilitas ---------- */
  const fac = { q: '', type: 'semua' };

  function renderFacTypes() {
    const items = [['semua', 'Semua']].concat(Object.entries(NA.FAC_TYPES));
    $('#fac-types').innerHTML = items.map(([k, l]) => pill(l, fac.type === k, 'data-type="' + k + '"', 'dark')).join('');
  }

  function renderFac() {
    const q = fac.q.trim().toLowerCase();
    const list = NA.FACILITIES.filter(
      (f) => (fac.type === 'semua' || f.type === fac.type) && (!q || (f.name + ' ' + f.city + ' ' + f.addr).toLowerCase().indexOf(q) !== -1)
    );
    $('#fac-list').innerHTML = list.length
      ? list
          .map(
            (f) =>
              '<article class="card fac"><span class="chip t-' + f.type + '">' + esc(NA.FAC_TYPES[f.type]) + '</span>' +
              '<h3>' + esc(f.name) + '</h3>' +
              '<div class="small muted">' + esc(f.city) + ' &middot; ' + esc(f.addr) + '</div>' +
              '<div class="small muted">Jam buka: ' + esc(f.hours) + '</div></article>'
          )
          .join('')
      : '<div class="empty">Tidak ada fasilitas yang cocok.</div>';
  }

  $('#fac-types').addEventListener('click', (e) => {
    const b = e.target.closest('button[data-type]');
    if (!b) return;
    fac.type = b.dataset.type;
    renderFacTypes();
    renderFac();
  });
  $('#fac-q').addEventListener('input', (e) => {
    fac.q = e.target.value;
    renderFac();
  });

  /* ---------- Mulai ---------- */
  renderAirPills();
  renderAir();
  loadAir();
  renderHealth();
  renderFilters();
  initMap();
  refreshFire();
  renderFacTypes();
  renderFac();
})();
