/* NafasAman - data.js
   Data bersama, helper, dan penyimpanan.
   Prototipe ini memakai localStorage sebagai pengganti backend/database.
   Saat dihubungkan ke server (Laravel/Express), ganti NA.fire.* dan NA.health.* dengan fetch() ke REST API. */
(function () {
  'use strict';
  const NA = (window.NA = {});

  /* ---------- Helper ---------- */
  NA.esc = (s) =>
    String(s == null ? '' : s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  NA.timeAgo = (ts) => {
    const m = Math.max(1, Math.round((Date.now() - ts) / 60000));
    if (m < 60) return m + ' menit lalu';
    const h = Math.round(m / 60);
    if (h < 24) return h + ' jam lalu';
    return Math.round(h / 24) + ' hari lalu';
  };

  NA.uid = () => Date.now().toString(36) + Math.random().toString(36).slice(2, 6);

  NA.store = {
    get(key, fallback) {
      try {
        const v = localStorage.getItem(key);
        return v ? JSON.parse(v) : fallback;
      } catch (e) {
        return fallback;
      }
    },
    set(key, value) {
      try {
        localStorage.setItem(key, JSON.stringify(value));
        return true;
      } catch (e) {
        return false;
      }
    },
    remove(key) {
      try { localStorage.removeItem(key); } catch (e) { /* abaikan */ }
    }
  };

  /* ---------- Konstanta ---------- */
  NA.LEVELS = {
    baik: 'Baik',
    sedang: 'Sedang',
    tidak: 'Tidak sehat',
    sangat: 'Sangat tidak sehat',
    bahaya: 'Berbahaya'
  };

  NA.CITIES = [
    { id: 'plk', name: 'Palangka Raya', lat: -2.2096, lng: 113.9213, sample: 312 },
    { id: 'ptk', name: 'Pontianak', lat: -0.0263, lng: 109.3425, sample: 178 },
    { id: 'jmb', name: 'Jambi', lat: -1.6101, lng: 103.6131, sample: 235 },
    { id: 'plg', name: 'Palembang', lat: -2.9761, lng: 104.7754, sample: 96 }
  ];

  NA.GROUPS = [
    { id: 'umum', name: 'Umum', tip: 'Minum air putih yang cukup dan perbanyak istirahat.' },
    { id: 'balita', name: 'Balita', tip: 'Jaga balita tetap di dalam ruangan dan jangan biarkan bermain di luar.' },
    { id: 'hamil', name: 'Ibu hamil', tip: 'Batasi keluar rumah dan segera konsultasi bila sesak atau pusing.' },
    { id: 'asma', name: 'Penderita asma', tip: 'Siapkan obat pelega sesuai anjuran dokter dan pantau gejala.' },
    { id: 'lansia', name: 'Lansia', tip: 'Lansia dengan sakit jantung atau paru perlu lebih banyak istirahat di dalam ruangan.' }
  ];

  NA.ADVICE = {
    baik: ['Aktivitas di luar ruangan aman dilakukan.', 'Tetap pantau kondisi udara secara berkala.', 'Tidak perlu masker khusus.'],
    sedang: ['Aktivitas di luar ruangan masih wajar.', 'Kelompok sensitif sebaiknya mengurangi aktivitas berat di luar.', 'Siapkan masker bila bau asap mulai tercium.'],
    tidak: ['Kurangi aktivitas berat di luar ruangan.', 'Pakai masker N95/KN95 saat harus keluar.', 'Tutup jendela dan pintu bila asap masuk.'],
    sangat: ['Hindari aktivitas di luar ruangan.', 'Pakai masker N95/KN95 bila terpaksa keluar.', 'Tetap di dalam ruangan dengan jendela tertutup.'],
    bahaya: ['Tetap di dalam ruangan dan tunda aktivitas di luar.', 'Pakai masker N95/KN95 bila terpaksa keluar.', 'Segera ke fasilitas kesehatan bila sesak napas.']
  };

  NA.SYMPTOMS = [
    { id: 'batuk', label: 'Batuk' },
    { id: 'sesak', label: 'Sesak napas' },
    { id: 'mata', label: 'Mata perih' },
    { id: 'tenggorokan', label: 'Sakit tenggorokan' },
    { id: 'kepala', label: 'Sakit kepala' },
    { id: 'pusing', label: 'Pusing' },
    { id: 'nyeri', label: 'Nyeri dada' },
    { id: 'hidung', label: 'Hidung iritasi' }
  ];
  NA.symLabel = (id) => (NA.SYMPTOMS.find((s) => s.id === id) || { label: id }).label;

  NA.DURATIONS = [
    { id: 'baru', name: 'Hari ini' },
    { id: 'sedang', name: '2-3 hari' },
    { id: 'lama', name: 'Lebih dari 3 hari' }
  ];

  NA.STATUS = {
    api: 'Masih terbakar',
    tangani: 'Dalam penanganan',
    padam: 'Sudah padam'
  };

  NA.FACILITIES = [
    { type: 'oksigen', name: 'Rumah Oksigen Kelurahan Bukit Tunggal', city: 'Palangka Raya', addr: 'Jl. Badak XVI', hours: '07.00 - 23.59' },
    { type: 'puskesmas', name: 'Puskesmas Marina Permai', city: 'Palangka Raya', addr: 'Jl. Marina Permai II No.65', hours: '07.00 - 14.30' },
    { type: 'posko', name: 'Posko Karhutla Kota Pontianak', city: 'Pontianak', addr: 'Jl. Karya Baru', hours: 'Buka 24 Jam' },
    { type: 'oksigen', name: 'Rumah Oksigen Kecamatan Danau Tlk', city: 'Jambi', addr: 'Jl. KH. M. Thayib No.12', hours: '09.00 - 22.00' },
    { type: 'puskesmas', name: 'Puskesmas Dempo', city: 'Palembang', addr: 'Jl. Kolonel Atmo No.861', hours: '07.30 - 14.00' },
    { type: 'posko', name: 'Posko Karhutla Kota Jambi', city: 'Jambi', addr: 'Jl. Pematang Raman', hours: 'Buka 24 Jam' }
  ];
  NA.FAC_TYPES = { oksigen: 'Rumah oksigen', puskesmas: 'Puskesmas', posko: 'Posko' };

  /* ---------- Kualitas udara ---------- */
  // Perkiraan ISPU dari PM2.5 (breakpoint ISPU: 15,5 / 55,4 / 150,4 / 250,4 ug/m3), interpolasi linear.
  NA.pm25ToIspu = (c) => {
    const bp = [
      [0, 15.5, 0, 50],
      [15.5, 55.4, 51, 100],
      [55.4, 150.4, 101, 200],
      [150.4, 250.4, 201, 300],
      [250.4, 500, 301, 500]
    ];
    const v = Math.max(0, c);
    for (const [cl, ch, il, ih] of bp) {
      if (v <= ch) return Math.round(il + ((v - cl) / (ch - cl)) * (ih - il));
    }
    return 500;
  };

  NA.levelKey = (ispu) => (ispu <= 50 ? 'baik' : ispu <= 100 ? 'sedang' : ispu <= 200 ? 'tidak' : ispu <= 300 ? 'sangat' : 'bahaya');

  // Data langsung dari Open-Meteo Air Quality API (gratis, tanpa API key). Di-cache 10 menit.
  NA.fetchPm25 = async (city) => {
    const key = 'na_aq_' + city.id;
    const cached = NA.store.get(key, null);
    if (cached && Date.now() - cached.t < 600000) return cached.v;
    const ctrl = new AbortController();
    const timer = setTimeout(() => ctrl.abort(), 6000);
    try {
      const url = 'https://air-quality-api.open-meteo.com/v1/air-quality?latitude=' + city.lat + '&longitude=' + city.lng + '&current=pm2_5';
      const res = await fetch(url, { signal: ctrl.signal });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const json = await res.json();
      const v = json && json.current && json.current.pm2_5;
      if (typeof v !== 'number') throw new Error('Data kosong');
      NA.store.set(key, { t: Date.now(), v });
      return v;
    } finally {
      clearTimeout(timer);
    }
  };

  /* ---------- Data contoh (seed) ---------- */
  const FIRE_KEY = 'na_fire';
  const HEALTH_KEY = 'na_health';
  const VOTE_KEY = 'na_votes';
  const SEED_KEY = 'na_seeded_v6';
  const MIN = 60000;

  function seed() {
    const now = Date.now();
    const fire = [
   { id: 'r1', place: 'Kel. Sabaru, Palangka Raya', desc: 'Api terlihat di lahan kosong dekat permukiman.', ts: now - 20 * MIN, st: 'api', lat: -2.23, lng: 113.9, yes: 12, no: 1, state: 'approved', photo: '/public/assets/img/1.jpg' },
{ id: 'r4', place: 'Kel. Bansir Darat, Pontianak', desc: 'Asap tebal dari lahan gambut di tepi jalan.', ts: now - 60 * MIN, st: 'api', lat: -0.05, lng: 109.32, yes: 7, no: 0, state: 'approved', photo: '/public/assets/img/2.jpg' },
{ id: 'r2', place: 'Kec. Berbak, Jambi', desc: 'Petugas sudah di lokasi, api mulai mengecil.', ts: now - 120 * MIN, st: 'tangani', lat: -1.6, lng: 103.64, yes: 5, no: 2, state: 'approved', photo: '/public/assets/img/3.webp' },
{ id: 'r3', place: 'Desa Sungai Rambutan, Ogan Ilir', desc: 'Api sudah padam, masih ada sisa asap tipis.', ts: now - 300 * MIN, st: 'padam', lat: -3.22, lng: 104.64, yes: 1, no: 9, state: 'approved', photo: '/public/assets/img/4.webp' },
{ id: 'p1', place: 'Kel. Karya Jaya, Palembang', desc: 'Api kecil di semak dekat jalan raya.', ts: now - 10 * MIN, st: 'api', lat: -2.95, lng: 104.76, yes: 0, no: 0, state: 'pending', photo: '/public/assets/img/5.webp' },
{ id: 'p2', place: 'Kec. Jekan Raya, Palangka Raya', desc: 'Petugas pemadam sedang di lokasi.', ts: now - 40 * MIN, st: 'tangani', lat: -2.19, lng: 113.95, yes: 0, no: 0, state: 'pending', photo: '/public/assets/img/6.webp' }
    ];
    NA.store.set(FIRE_KEY, fire);

    // Laporan gejala contoh dibuat deterministik agar grafik tidak berubah-ubah.
    const spec = [['Kel. Sabaru', 'Palangka Raya', 64], ['Kel. Bansir Darat', 'Pontianak', 48], ['Kec. Berbak, Jambi', 'Jambi', 31], ['Desa Sungai Rambutan', 'Ogan Ilir', 12], ['Kel. Karya Jaya', 'Palembang', 9]];
    const groups = ['umum', 'umum', 'balita', 'umum', 'asma', 'lansia', 'hamil'];
    const durs = ['baru', 'sedang', 'lama'];
    const health = [];
    spec.forEach(([kel, kota, n]) => {
      for (let i = 0; i < n; i++) {
        const sym = [];
        if (i % 5 !== 4) sym.push('batuk');
        if (i % 3 === 0) sym.push('mata');
        if (i % 7 === 0) sym.push('sesak');
        if (i % 4 === 0) sym.push('tenggorokan');
        if (!sym.length) sym.push('pusing');
        health.push({ id: 'h' + health.length, kota, kel, symptoms: sym, dur: durs[i % 3], group: groups[i % groups.length], ts: now - ((i * 97) % (7 * 24 * 60)) * MIN });
      }
    });
    NA.store.set(HEALTH_KEY, health);
    NA.store.set(SEED_KEY, 1);
  }
  if (!NA.store.get(SEED_KEY, 0)) seed();

  NA.resetSample = () => {
    [FIRE_KEY, HEALTH_KEY, VOTE_KEY, SEED_KEY].forEach(NA.store.remove);
    seed();
  };

  /* ---------- Akses data ---------- */
  NA.fire = {
    all: () => NA.store.get(FIRE_KEY, []),
    save: (list) => NA.store.set(FIRE_KEY, list),
    add(report) {
      const list = NA.fire.all();
      list.unshift(report);
      return NA.fire.save(list);
    },
    update(id, fn) {
      const list = NA.fire.all();
      const r = list.find((x) => x.id === id);
      if (r) fn(r);
      NA.fire.save(list);
    },
    remove: (id) => NA.fire.save(NA.fire.all().filter((x) => x.id !== id)),
    votes: () => NA.store.get(VOTE_KEY, {}),
    saveVotes: (v) => NA.store.set(VOTE_KEY, v)
  };

  NA.health = {
    all: () => NA.store.get(HEALTH_KEY, []),
    add(report) {
      const list = NA.health.all();
      list.push(report);
      return NA.store.set(HEALTH_KEY, list);
    },
    byWilayah() {
      const map = {};
      NA.health.all().forEach((r) => {
        const name = r.kel + ', ' + r.kota;
        map[name] = (map[name] || 0) + 1;
      });
      return Object.entries(map).map(([name, n]) => ({ name, n })).sort((a, b) => b.n - a.n);
    },
    bySymptom() {
      const map = {};
      NA.health.all().forEach((r) => r.symptoms.forEach((s) => (map[s] = (map[s] || 0) + 1)));
      return Object.entries(map).map(([id, n]) => ({ id, n })).sort((a, b) => b.n - a.n);
    }
  };
})();
