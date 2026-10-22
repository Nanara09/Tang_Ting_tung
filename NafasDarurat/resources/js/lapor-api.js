/* NafasDarurat - lapor-api.js
   Lapor titik api: pilih lokasi di peta (Leaflet + OpenStreetMap), status, foto, keterangan. */
(function () {
  'use strict';
  const { esc } = NA;
  const $ = (sel) => document.querySelector(sel);
  const err = $('#err');

  /* ---------- Status ---------- */
  const DOTS = { api: '#D92D20', tangani: '#E8920B', padam: '#1E8A4C' };
  $('#status-opts').innerHTML = Object.keys(NA.STATUS)
    .map(
      (k, i) =>
        '<label class="opt"><input type="radio" name="status" value="' + k + '"' + (i === 0 ? ' checked' : '') + '>' +
        '<i class="st-dot" style="--dot:' + DOTS[k] + '" aria-hidden="true"></i><span>' + esc(NA.STATUS[k]) + '</span></label>'
    )
    .join('');

  /* ---------- Peta pemilih lokasi ---------- */
  const pos = { lat: null, lng: null };
  let map = null;
  let marker = null;

  function setCoords(lat, lng) {
    pos.lat = lat;
    pos.lng = lng;
    $('#coords').textContent = 'Koordinat: ' + lat.toFixed(5) + ', ' + lng.toFixed(5);
  }

  function setPin(lat, lng, zoom) {
    if (!marker) {
      marker = L.marker([lat, lng], { draggable: true, title: 'Geser pin ke titik api' }).addTo(map);
      marker.on('dragend', () => {
        const p = marker.getLatLng();
        setCoords(p.lat, p.lng);
      });
    } else {
      marker.setLatLng([lat, lng]);
    }
    setCoords(lat, lng);
    if (zoom) map.setView([lat, lng], zoom);
  }

  if (typeof L === 'undefined') {
    $('#pick-map').innerHTML = '<div class="empty" style="margin:12px">Peta tidak bisa dimuat. Periksa koneksi internet.</div>';
  } else {
    map = L.map('pick-map').setView([-2.2, 108.3], 5);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    map.on('click', (e) => setPin(e.latlng.lat, e.latlng.lng));
  }

  $('#btn-gps').addEventListener('click', () => {
    err.textContent = '';
    if (!map) { err.textContent = 'Peta belum siap.'; return; }
    if (!navigator.geolocation) { err.textContent = 'Browser ini tidak mendukung lokasi.'; return; }
    const btn = $('#btn-gps');
    btn.disabled = true;
    navigator.geolocation.getCurrentPosition(
      (p) => {
        btn.disabled = false;
        setPin(p.coords.latitude, p.coords.longitude, 15);
      },
      () => {
        btn.disabled = false;
        err.textContent = 'Lokasi tidak bisa diambil. Izinkan akses lokasi atau ketuk peta secara manual.';
      },
      { enableHighAccuracy: true, timeout: 10000 }
    );
  });

  /* ---------- Foto: validasi, perkecil, hapus EXIF ---------- */
  let photo = '';

  function resizeImage(file, max) {
    return new Promise((resolve, reject) => {
      const url = URL.createObjectURL(file);
      const img = new Image();
      img.onload = () => {
        const s = Math.min(1, max / Math.max(img.width, img.height));
        const c = document.createElement('canvas');
        c.width = Math.round(img.width * s);
        c.height = Math.round(img.height * s);
        c.getContext('2d').drawImage(img, 0, 0, c.width, c.height);
        URL.revokeObjectURL(url);
        resolve(c.toDataURL('image/jpeg', 0.72));
      };
      img.onerror = () => {
        URL.revokeObjectURL(url);
        reject(new Error('Gambar tidak bisa dibaca.'));
      };
      img.src = url;
    });
  }

  $('#foto').addEventListener('change', async (e) => {
    err.textContent = '';
    photo = '';
    $('#preview').style.display = 'none';
    const f = e.target.files[0];
    if (!f) return;
    if (['image/jpeg', 'image/png'].indexOf(f.type) === -1) { err.textContent = 'Foto harus berformat JPG atau PNG.'; e.target.value = ''; return; }
    if (f.size > 2 * 1024 * 1024) { err.textContent = 'Ukuran foto maksimal 2 MB.'; e.target.value = ''; return; }
    try {
      photo = await resizeImage(f, 800);
      $('#preview-img').src = photo;
      $('#preview').style.display = 'block';
    } catch (ex) {
      err.textContent = ex.message;
      e.target.value = '';
    }
  });

  /* ---------- Kirim ---------- */
  let lastSubmit = 0;
  $('#form').addEventListener('submit', (e) => {
    e.preventDefault();
    err.textContent = '';
    const place = $('#place').value.trim().replace(/\s+/g, ' ');
    const desc = $('#desc').value.trim().slice(0, 300);
    const st = document.querySelector('input[name="status"]:checked').value;

    if (!place) { err.textContent = 'Isi nama lokasi.'; $('#place').focus(); return; }
    if (pos.lat === null) { err.textContent = 'Tandai lokasi di peta atau gunakan lokasi saya.'; return; }
    if (Date.now() - lastSubmit < 10000) { err.textContent = 'Tunggu sebentar sebelum mengirim lagi.'; return; }

    // state "pending": belum tampil di peta publik sampai admin menyetujui.
    const ok = NA.fire.add({
      id: NA.uid(), place, desc: desc || '-', ts: Date.now(), st,
      lat: pos.lat, lng: pos.lng, yes: 0, no: 0, state: 'pending', photo
    });
    if (!ok) { err.textContent = 'Penyimpanan di browser penuh. Coba kirim tanpa foto.'; return; }
    lastSubmit = Date.now();
    $('#form').hidden = true;
    $('#done').hidden = false;
    $('#done').scrollIntoView({ behavior: 'smooth', block: 'center' });
  });
})();
