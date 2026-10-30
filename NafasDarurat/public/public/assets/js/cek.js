/* NafasAman - cek.js
   Pemeriksa gejala berbasis aturan sederhana (tanpa AI), mudah dijelaskan dan diuji. */
(function () {
  'use strict';
  const { esc } = NA;
  const $ = (sel) => document.querySelector(sel);

  const RESULTS = {
    none: { label: 'Belum ada gejala dipilih', cls: 'lv-none', tips: ['Pilih gejala yang kamu rasakan untuk melihat saran.'] },
    ringan: {
      label: 'Gejala ringan', cls: 'lv-sedang',
      tips: ['Kurangi aktivitas di luar ruangan.', 'Pakai masker saat keluar dan tutup jendela.', 'Pantau gejalamu dan cek lagi besok.']
    },
    pantau: {
      label: 'Perlu dipantau', cls: 'lv-tidak',
      tips: ['Tetap di dalam ruangan; pakai masker N95/KN95 bila harus keluar.', 'Perbanyak minum air putih dan istirahat.', 'Periksa ke puskesmas bila gejala berlanjut lebih dari 2 hari.']
    },
    urgent: {
      label: 'Segera ke fasilitas kesehatan', cls: 'lv-sangat',
      tips: ['Sesak napas atau nyeri dada perlu diperiksa tenaga kesehatan segera.', 'Jangan menunggu gejala membaik, cari bantuan medis sekarang.', 'Minta orang lain menemani dan menjauh dari asap.']
    }
  };

  // Aturan: sesak/nyeri dada = urgent; >=3 gejala atau kelompok rentan dengan >=1 gejala = pantau; 1-2 gejala = ringan.
  function assess(symptoms, group) {
    const count = symptoms.length;
    const urgent = symptoms.indexOf('sesak') !== -1 || symptoms.indexOf('nyeri') !== -1;
    const sensitive = group !== 'umum';
    let key = 'none';
    if (urgent) key = 'urgent';
    else if (count >= 3 || (sensitive && count >= 1)) key = 'pantau';
    else if (count >= 1) key = 'ringan';
    const tips = RESULTS[key].tips.slice();
    if (sensitive && count >= 1) tips.push('Kelompok rentan lebih berisiko, konsultasikan dengan tenaga kesehatan.');
    return { key, tips, count };
  }
  NA.assess = assess;

  $('#sym-grid').innerHTML = NA.SYMPTOMS.map(
    (s) =>
      '<label class="check"><input type="checkbox" name="gejala" value="' + esc(s.id) + '">' +
      '<span class="box" aria-hidden="true"></span><span>' + esc(s.label) + '</span></label>'
  ).join('');

  $('#group-opts').innerHTML = NA.GROUPS.map(
    (g, i) =>
      '<label class="opt pillish"><input type="radio" name="kelompok" value="' + esc(g.id) + '"' + (i === 0 ? ' checked' : '') + '>' +
      '<span>' + esc(g.name) + '</span></label>'
  ).join('');

  function selected() {
    const symptoms = Array.from(document.querySelectorAll('input[name="gejala"]:checked')).map((i) => i.value);
    const group = (document.querySelector('input[name="kelompok"]:checked') || { value: 'umum' }).value;
    return { symptoms, group };
  }

  function render() {
    const { symptoms, group } = selected();
    const r = assess(symptoms, group);
    const info = RESULTS[r.key];
    $('#count').textContent = r.count + ' gejala dipilih';
    $('#res-chip').className = 'chip lg ' + info.cls;
    $('#res-label').textContent = info.label;
    $('#res-tips').innerHTML = r.tips.map((t) => '<li>' + esc(t) + '</li>').join('');
  }

  $('#cek-form').addEventListener('change', render);
  $('#cek-form').addEventListener('submit', (e) => e.preventDefault());

  // Bawa pilihan ke form lapor supaya tidak mengisi ulang.
  $('#to-report').addEventListener('click', () => {
    try {
      sessionStorage.setItem('na_prefill', JSON.stringify(selected()));
    } catch (e) { /* abaikan */ }
  });

  render();
})();
