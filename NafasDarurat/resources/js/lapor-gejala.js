(function () {
  'use strict';
  const { esc } = NA;
  const $ = (sel) => document.querySelector(sel);

  $('#kota').insertAdjacentHTML('beforeend', NA.CITIES.map((c) => '<option value="' + esc(c.name) + '">' + esc(c.name) + '</option>').join(''));

  $('#sym-grid').innerHTML = NA.SYMPTOMS.map(
    (s) =>
      '<label class="check"><input type="checkbox" name="gejala" value="' + esc(s.id) + '">' +
      '<span class="box" aria-hidden="true"></span><span>' + esc(s.label) + '</span></label>'
  ).join('');

  $('#dur-opts').innerHTML = NA.DURATIONS.map(
    (d, i) =>
      '<label class="opt"><input type="radio" name="dur" value="' + esc(d.id) + '"' + (i === 0 ? ' checked' : '') + '>' +
      '<span>' + esc(d.name) + '</span></label>'
  ).join('');

  $('#group-opts').innerHTML = NA.GROUPS.map(
    (g, i) =>
      '<label class="opt pillish"><input type="radio" name="kelompok" value="' + esc(g.id) + '"' + (i === 0 ? ' checked' : '') + '>' +
      '<span>' + esc(g.name) + '</span></label>'
  ).join('');

  // Isi otomatis dari halaman Cek kesehatan.
  try {
    const pre = JSON.parse(sessionStorage.getItem('na_prefill') || 'null');
    if (pre) {
      (pre.symptoms || []).forEach((id) => {
        const el = document.querySelector('input[name="gejala"][value="' + id + '"]');
        if (el) el.checked = true;
      });
      const g = document.querySelector('input[name="kelompok"][value="' + pre.group + '"]');
      if (g) g.checked = true;
      sessionStorage.removeItem('na_prefill');
    }
  } catch (e) { /* abaikan */ }

  const err = $('#err');
  let lastSubmit = 0;

  $('#form').addEventListener('submit', (e) => {
    e.preventDefault();
    err.textContent = '';
    const nama = $('#nama').value.trim().replace(/\s+/g, ' ');
    const kota = $('#kota').value.trim().replace(/\s+/g, ' ');
    const kel = $('#kel').value.trim().replace(/\s+/g, ' ');
    const symptoms = Array.from(document.querySelectorAll('input[name="gejala"]:checked')).map((i) => i.value);
    const dur = document.querySelector('input[name="dur"]:checked').value;
    const group = document.querySelector('input[name="kelompok"]:checked').value;

    if (!nama) { err.textContent = 'Isi nama pelapor.'; $('#nama').focus(); return; }
    if (nama.length > 255) { err.textContent = 'Nama maksimal 255 karakter.'; $('#nama').focus(); return; }
    if (!kota) { err.textContent = 'Pilih nama kota.'; $('#kota').focus(); return; }
    if (!kel) { err.textContent = 'Isi kelurahan atau desa.'; $('#kel').focus(); return; }
    if (!symptoms.length) { err.textContent = 'Pilih minimal satu gejala.'; return; }
    if (Date.now() - lastSubmit < 10000) { err.textContent = 'Tunggu sebentar sebelum mengirim lagi.'; return; }

    const ok = NA.health.add({ id: NA.uid(), kota, kel, symptoms, dur, group, ts: Date.now() });
    if (!ok) { err.textContent = 'Penyimpanan di browser penuh. Coba lagi nanti.'; return; }
    lastSubmit = Date.now();
    $('#form').hidden = true;
    $('#done').hidden = false;
    $('#done').scrollIntoView({ behavior: 'smooth', block: 'center' });
  });
})();
