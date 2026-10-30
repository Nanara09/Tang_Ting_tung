/* NafasAman - admin.js
   Panel admin: verifikasi titik api, ringkasan gejala, ekspor CSV.
   PENTING: login di sini hanya demo di sisi klien dan TIDAK aman.
   Di produksi, autentikasi dan otorisasi harus dilakukan di server. */
(function () {
  'use strict';
  const { esc, timeAgo } = NA;
  const $ = (sel) => document.querySelector(sel);

  const DEMO_USER = 'admin@gmail.com';
  const DEMO_PASS = 'niagarafruit';

  const authed = () => {
    try { return sessionStorage.getItem('na_admin') === '1'; } catch (e) { return false; }
  };

  function show() {
    const ok = authed();
    $('#gate').hidden = ok;
    $('#panel').hidden = !ok;
    if (ok) renderAll();
  }

  $('#login').addEventListener('submit', (e) => {
    e.preventDefault();
    if ($('#u').value === DEMO_USER && $('#p').value === DEMO_PASS) {
      try { sessionStorage.setItem('na_admin', '1'); } catch (ex) { /* abaikan */ }
      $('#login-err').textContent = '';
      $('#p').value = '';
      show();
    } else {
      $('#login-err').textContent = 'Pengguna atau kata sandi salah.';
    }
  });

  $('#btn-logout').addEventListener('click', () => {
    try { sessionStorage.removeItem('na_admin'); } catch (e) { /* abaikan */ }
    show();
  });

  /* ---------- Ringkasan angka ---------- */
  function renderStats() {
    const fire = NA.fire.all();
    const health = NA.health.all();
    const dayAgo = Date.now() - 24 * 60 * 60 * 1000;
    const items = [
      ['Gejala dilaporkan (24 jam)', health.filter((h) => h.ts > dayAgo).length],
      ['Titik api menunggu verifikasi', fire.filter((r) => r.state === 'pending').length],
      ['Titik api masih terbakar', fire.filter((r) => r.state === 'approved' && r.st === 'api').length],
      ['Laporan ditolak', fire.filter((r) => r.state === 'rejected').length]
    ];
    $('#stats').innerHTML = items.map(([l, n]) => '<div class="stat"><span>' + esc(l) + '</span><strong>' + n + '</strong></div>').join('');
  }

  /* ---------- Tab Titik api ---------- */
  const STATE = {
    pending: { label: 'Menunggu', cls: 'lv-sedang' },
    approved: { label: 'Disetujui', cls: 'lv-baik' },
    rejected: { label: 'Ditolak', cls: 'lv-sangat' }
  };
  const ORDER = { pending: 0, approved: 1, rejected: 2 };
  const PHOTO_ICON =
    '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2.5" stroke="currentColor" stroke-width="1.8"></rect><circle cx="9" cy="10.5" r="1.8" stroke="currentColor" stroke-width="1.6"></circle><path d="M4 17l5-4.5 3.5 3L16 12l4 4" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"></path></svg>';

  function renderFire() {
    const list = NA.fire.all().sort((a, b) => ORDER[a.state] - ORDER[b.state] || b.ts - a.ts);
    $('#fire-body').innerHTML = list.length
      ? list
          .map((r) => {
            const st = STATE[r.state] || STATE.pending;
            const photo = r.photo && r.photo.indexOf('data:image/') === 0
              ? '<img class="thumb" src="' + r.photo + '" alt="Foto laporan ' + esc(r.place) + '">'
              : '<span class="thumb" aria-hidden="true">' + PHOTO_ICON + '</span>';
            const actions = [];
            if (r.state !== 'approved') actions.push('<button type="button" class="btn-sm ok" data-act="approve" aria-label="Setujui laporan ' + esc(r.place) + '">Setujui</button>');
            if (r.state !== 'rejected') actions.push('<button type="button" class="btn-sm bad" data-act="reject" aria-label="Tolak laporan ' + esc(r.place) + '">Tolak</button>');
            actions.push('<button type="button" class="btn-sm" data-act="delete" aria-label="Hapus laporan ' + esc(r.place) + '">Hapus</button>');
            return (
              '<tr data-id="' + esc(r.id) + '"><td>' + photo + '</td>' +
              '<td><b>' + esc(r.place) + '</b><br><span class="muted">' + esc(r.desc) + '</span><br>' +
              '<span class="small muted">' + Number(r.lat).toFixed(4) + ', ' + Number(r.lng).toFixed(4) + '</span></td>' +
              '<td>' + esc(timeAgo(r.ts)) + '</td>' +
              '<td><span class="chip st-' + esc(r.st) + '"><i class="dot"></i>' + esc(NA.STATUS[r.st] || r.st) + '</span></td>' +
              '<td><span class="chip ' + st.cls + '">' + st.label + '</span></td>' +
              '<td><div class="row">' + actions.join('') + '</div></td></tr>'
            );
          })
          .join('')
      : '<tr><td colspan="6" class="muted">Belum ada laporan titik api.</td></tr>';
  }

  $('#fire-body').addEventListener('click', (e) => {
    const b = e.target.closest('button[data-act]');
    const row = e.target.closest('tr[data-id]');
    if (!b || !row) return;
    const id = row.dataset.id;
    if (b.dataset.act === 'delete') {
      if (!confirm('Hapus laporan ini secara permanen?')) return;
      NA.fire.remove(id);
    } else {
      NA.fire.update(id, (r) => { r.state = b.dataset.act === 'approve' ? 'approved' : 'rejected'; });
    }
    renderAll();
  });

  /* ---------- Tab Gejala warga ---------- */
  const DUR = { baru: 'Hari ini', sedang: '2-3 hari', lama: 'Lebih dari 3 hari' };

  function renderHealth() {
    const all = NA.health.all();
    const groups = {};
    all.forEach((r) => {
      const name = r.kel + ', ' + r.kota;
      const g = groups[name] || (groups[name] = { n: 0, sym: {} });
      g.n++;
      r.symptoms.forEach((s) => (g.sym[s] = (g.sym[s] || 0) + 1));
    });
    const rows = Object.entries(groups).sort((a, b) => b[1].n - a[1].n);
    $('#health-body').innerHTML = rows.length
      ? rows
          .map(([name, g]) => {
            const top = Object.entries(g.sym).sort((a, b) => b[1] - a[1]).slice(0, 2).map(([id, n]) => NA.symLabel(id) + ' (' + n + ')').join(', ');
            return '<tr><td><b>' + esc(name) + '</b></td><td>' + g.n + '</td><td>' + esc(top) + '</td></tr>';
          })
          .join('')
      : '<tr><td colspan="3" class="muted">Belum ada laporan gejala.</td></tr>';

    const recent = all.slice().sort((a, b) => b.ts - a.ts).slice(0, 10);
    $('#recent-body').innerHTML = recent
      .map((r) => {
        const g = NA.GROUPS.find((x) => x.id === r.group);
        return (
          '<tr><td>' + esc(timeAgo(r.ts)) + '</td><td>' + esc(r.kel + ', ' + r.kota) + '</td>' +
          '<td>' + esc(r.symptoms.map(NA.symLabel).join(', ')) + '</td>' +
          '<td>' + esc(DUR[r.dur] || r.dur) + '</td><td>' + esc(g ? g.name : r.group) + '</td></tr>'
        );
      })
      .join('');
  }

  // Ekspor CSV. Sel yang diawali = + - @ diberi tanda ' agar tidak dieksekusi sebagai rumus di spreadsheet.
  function csvCell(v) {
    let s = String(v == null ? '' : v);
    if (/^[=+\-@]/.test(s)) s = "'" + s;
    return '"' + s.replace(/"/g, '""') + '"';
  }

  $('#btn-csv').addEventListener('click', () => {
    const head = ['id', 'waktu', 'kota', 'kelurahan', 'gejala', 'lama', 'kelompok'];
    const lines = [head.join(',')].concat(
      NA.health.all().map((r) =>
        [r.id, new Date(r.ts).toISOString(), r.kota, r.kel, r.symptoms.join('|'), r.dur, r.group].map(csvCell).join(',')
      )
    );
    const blob = new Blob(['﻿' + lines.join('\n')], { type: 'text/csv;charset=utf-8' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'laporan-gejala.csv';
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(() => URL.revokeObjectURL(a.href), 1000);
  });

  /* ---------- Tab & reset ---------- */
  function selectTab(which) {
    const fire = which === 'fire';
    $('#tab-fire').setAttribute('aria-selected', fire);
    $('#tab-health').setAttribute('aria-selected', !fire);
    $('#pane-fire').hidden = !fire;
    $('#pane-health').hidden = fire;
  }
  $('#tab-fire').addEventListener('click', () => selectTab('fire'));
  $('#tab-health').addEventListener('click', () => selectTab('health'));

  $('#btn-reset').addEventListener('click', () => {
    if (!confirm('Kembalikan semua data ke data contoh? Laporan yang kamu kirim akan hilang.')) return;
    NA.resetSample();
    renderAll();
  });

  function renderAll() {
    renderStats();
    renderFire();
    renderHealth();
  }

  show();
})();
