/* Nafas Darurat - app.js
   Pengganti data.js + common.js versi demo. Konstanta datang dari config/nafasdarurat.php
   (window.NA_CONFIG), data dari endpoint JSON Laravel (bukan localStorage lagi). */
(function () {
  'use strict';
  const NA = (window.NA = {});
  const C = window.NA_CONFIG || {};

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

  /* ---------- Konstanta (dari config Laravel) ---------- */
  NA.CITIES = C.cities || [];
  NA.GROUPS = C.groups || [];
  NA.LEVELS = C.levels || {};
  NA.ADVICE = C.advice || {};
  NA.SYMPTOMS = C.symptoms || [];
  NA.DURATIONS = C.durations || [];
  NA.STATUS = C.status || {};
  NA.FAC_TYPES = C.fac_types || {};
  NA.symLabel = (id) => (NA.SYMPTOMS.find((s) => s.id === id) || { label: id }).label;
  NA.levelKey = (ispu) => (ispu <= 50 ? 'baik' : ispu <= 100 ? 'sedang' : ispu <= 200 ? 'tidak' : ispu <= 300 ? 'sangat' : 'bahaya');

  /* ---------- HTTP ---------- */
  const csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
  const base = (window.NA_BASE || '').replace(/\/$/, '');

  function firstError(data) {
    if (!data) return '';
    if (data.errors) {
      const k = Object.keys(data.errors)[0];
      if (k) return data.errors[k][0];
    }
    return data.message || '';
  }

  async function request(method, path, body) {
    const opts = { method, headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' } };
    if (body instanceof FormData) {
      opts.body = body;
    } else if (body) {
      opts.headers['Content-Type'] = 'application/json';
      opts.body = JSON.stringify(body);
    }
    let res;
    try {
      res = await fetch(base + path, opts);
    } catch (e) {
      throw new Error('Tidak bisa terhubung ke server. Periksa koneksi internet.');
    }
    let data = null;
    try { data = await res.json(); } catch (e) { /* bukan JSON */ }
    if (!res.ok) {
      const msg = res.status === 429 ? 'Terlalu banyak permintaan, coba lagi sebentar.' : firstError(data) || 'Terjadi kesalahan (' + res.status + ').';
      const err = new Error(msg);
      err.status = res.status;
      throw err;
    }
    return data;
  }

  /* ---------- Akses data (pengganti NA.fire / NA.health versi demo) ---------- */
  NA.api = {
    air: () => request('GET', '/api/udara'),
    facilities: () => request('GET', '/api/fasilitas'),
    healthSummary: () => request('GET', '/api/gejala/ringkasan'),
    healthAdd: (payload) => request('POST', '/api/gejala', payload),
    fireList: () => request('GET', '/api/titik-api'),
    fireAdd: (formData) => request('POST', '/api/titik-api', formData),
    fireVote: (id, choice) => request('POST', '/api/titik-api/' + encodeURIComponent(id) + '/vote', { choice })
  };
})();
