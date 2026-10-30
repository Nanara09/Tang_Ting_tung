/* NafasAman - reveal.js: animasi muncul saat discroll (full JS) */
(function () {
  'use strict';

  function init() {
    // Elemen yang diberi animasi. Tambah/kurangi sesuai selera.
    var targets = document.querySelectorAll(
      '.hero-copy, .air, .section .title, .section .sub, .card, .map-layout, .toolbar, .cta'
    );

    // Browser lama tanpa IntersectionObserver: biarkan tampil biasa
    if (!('IntersectionObserver' in window)) return;

   var io = new IntersectionObserver(function (entries) {
  entries.forEach(function (e) {
    if (e.isIntersecting) {
      // Masuk layar: munculkan
      e.target.style.opacity = '1';
      e.target.style.transform = 'translateY(0)';
    } else {
      e.target.style.opacity = '0';
      var keluarAtas = e.boundingClientRect.top < 0;
      e.target.style.transform = keluarAtas ? 'translateY(-30px)' : 'translateY(30px)';
}
  });
}, { threshold: 0.1 });

    targets.forEach(function (el) {
      // Jeda bertahap untuk elemen bersebelahan (misal kartu dalam grid)
      var idx = Array.prototype.indexOf.call(el.parentElement.children, el);
      var delay = Math.min(idx, 4) * 100;

      // Keadaan awal: tersembunyi & turun sedikit
      el.style.opacity = '0';
      el.style.transform = 'translateY(30px)';
      el.style.transition = 'opacity 0.7s ease ' + delay + 'ms, transform 0.7s ease ' + delay + 'ms';
      el.style.willChange = 'opacity, transform';

      io.observe(el);
    });
  }

  // Tunggu halaman & index.js selesai membuat konten
  if (document.readyState === 'complete') {
    setTimeout(init, 50);
  } else {
    window.addEventListener('load', function () { setTimeout(init, 50); });
  }
})();

/* NafasAman - reveal.js: kota berganti otomatis + animasi (berulang terus) */
(function () {
  'use strict';

  var URUTAN = ['Palangka Raya', 'Pontianak', 'Jambi', 'Palembang'];
  var JEDA = 3000;        // ganti kota tiap 5 detik
  var JEDA_KLIK = 15000;  // jeda 15 detik setelah pengguna klik sendiri
  var DURASI = 350;       // lama animasi geser (ms)

  var indeks = 0;
  var timer = null;
  var dariScript = false;

  /* Selalu ambil tombol TERBARU dari halaman (karena index.js bisa membuat ulang tombol) */
  function ambilTombol() {
    var wadah = document.getElementById('city-pills');
    if (!wadah) return [];
    var semua = Array.prototype.slice.call(wadah.querySelectorAll('button, a, [role="button"]'));
    var hasil = [];
    URUTAN.forEach(function (nama) {
      var b = semua.find(function (x) { return x.textContent.indexOf(nama) !== -1; });
      if (b) hasil.push(b);
    });
    return hasil.length >= 2 ? hasil : semua; // kalau nama tidak cocok, pakai urutan di halaman
  }

  /* ---------- Animasi angka ISPU berhitung naik ---------- */
  var nilaiKita = null;
  var frame = null;
  var animasiJalan = false;   // [BARU] penanda animasi angka sudah dimulai
  function hitungNaik(el, target) {
    cancelAnimationFrame(frame);
    animasiJalan = true;      // [BARU]
    var t0 = null;
    function langkah(t) {
      if (!t0) t0 = t;
      var p = Math.min((t - t0) / 700, 1);
      var v = String(Math.round(target * (1 - Math.pow(1 - p, 3))));
      nilaiKita = v;
      el.textContent = v;
      if (p < 1) frame = requestAnimationFrame(langkah);
    }
    frame = requestAnimationFrame(langkah);
  }
  function pantauAngka() {
    var kartu = document.getElementById('cek');
    if (!kartu || !('MutationObserver' in window)) return;
    new MutationObserver(function () {
      var el = document.getElementById('air-ispu');
      if (!el) return;
      var teks = el.textContent.trim();
      if (teks === nilaiKita) return;      // perubahan dari animasi sendiri
      var n = parseInt(teks, 10);
      if (!isNaN(n)) hitungNaik(el, n);    // nilai baru dari index.js
    }).observe(kartu, { childList: true, characterData: true, subtree: true });
  }

  /* ---------- Animasi geser keluar / masuk ---------- */
  function atur(el, x, op, d) {
    el.style.transition = d ? 'opacity ' + d + 'ms ease, transform ' + d + 'ms ease' : 'none';
    el.style.transform = 'translateX(' + x + 'px)';
    el.style.opacity = op;
  }
  function bagian() {
    return [document.getElementById('air-result'), document.getElementById('air-tips')].filter(Boolean);
  }
  function denyut(btn) {
    if (!btn) return;
    btn.style.transition = 'transform .25s ease';
    btn.style.transform = 'scale(1.1)';
    setTimeout(function () { btn.style.transform = 'scale(1)'; }, 250);
  }

  /* ---------- Pindah ke kota ke-i ---------- */
  function pilih(i) {
    bagian().forEach(function (el) { atur(el, -40, 0, DURASI); });      // keluar ke kiri
    setTimeout(function () {
      var tombol = ambilTombol();
      if (!tombol.length) return;

      nilaiKita = null;        // [BARU] reset: angka berikutnya pasti dianggap baru
      animasiJalan = false;    // [BARU]

      dariScript = true;
      tombol[i % tombol.length].click();
      dariScript = false;

      // [BARU] Cadangan: kalau angkanya sama dengan kota sebelumnya, paksa animasi dari 0
      setTimeout(function () {
        if (animasiJalan) return;
        var el = document.getElementById('air-ispu');
        var n = el ? parseInt(el.textContent, 10) : NaN;
        if (!isNaN(n)) hitungNaik(el, n);
      }, 80);

      bagian().forEach(function (el) { atur(el, 40, 0, 0); });          // siap di kanan
      void document.body.offsetWidth;
      bagian().forEach(function (el) { atur(el, 0, 1, DURASI); });      // masuk dari kanan

      denyut(ambilTombol()[i % tombol.length]);                         // tombol baru yang aktif
    }, DURASI);
  }

  function berikutnya() {
    if (document.hidden) return;
    var jumlah = ambilTombol().length;
    if (jumlah < 2) return;
    indeks = (indeks + 1) % jumlah;   // setelah kota terakhir kembali ke 0 (Palangka Raya)
    pilih(indeks);
  }

  function mulai() {
    clearInterval(timer);
    clearTimeout(timer);
    timer = setInterval(berikutnya, JEDA);
  }

  /* ---------- Klik dari pengguna (delegasi, aman walau tombol dibuat ulang) ---------- */
  function pantauKlik() {
    var wadah = document.getElementById('city-pills');
    wadah.addEventListener('click', function (e) {
      if (dariScript) return;
      var btn = e.target.closest('button, a, [role="button"]');
      if (!btn) return;
      var i = ambilTombol().indexOf(btn);
      if (i !== -1) indeks = i;
      clearInterval(timer);
      clearTimeout(timer);
      timer = setTimeout(mulai, JEDA_KLIK);
    }, true);
  }

  /* ---------- Mulai: tunggu sampai tombol kota sudah dibuat index.js ---------- */
  function init(coba) {
    if (ambilTombol().length < 2) {
      if ((coba || 0) < 50) setTimeout(function () { init((coba || 0) + 1); }, 100);
      return;
    }
    pantauAngka();
    pantauKlik();
    indeks = 0;
    var t = ambilTombol();
    dariScript = true;
    t[0].click();          // mulai dari Palangka Raya
    dariScript = false;
    mulai();
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function () { init(0); });
  else init(0);
})();

/* ============ Animasi grafik "Gejala per wilayah" ============ */
(function () {
  'use strict';

  var DURASI = 1000;   // lama batang tumbuh (ms)
  var JEDA_BARIS = 120; // jeda antar baris (ms)

  // Batang isi = elemen di dalam #bars yang punya lebar persen (misal width: 75%)
  function ambilBatang(bars) {
    return Array.prototype.filter.call(bars.querySelectorAll('*'), function (el) {
      return el.style && el.style.width && el.style.width.indexOf('%') !== -1;
    });
  }

  // Angka = elemen paling dalam yang isinya hanya angka
  function ambilAngka(bars) {
    return Array.prototype.filter.call(bars.querySelectorAll('*'), function (el) {
      return el.children.length === 0 && /^\d+$/.test(el.textContent.trim());
    });
  }

  function hitung(el, target, delay) {
    el.textContent = '0';
    setTimeout(function () {
      var t0 = null;
      function langkah(t) {
        if (!t0) t0 = t;
        var p = Math.min((t - t0) / DURASI, 1);
        el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
        if (p < 1) requestAnimationFrame(langkah);
      }
      requestAnimationFrame(langkah);
    }, delay);
  }

  function jalankan() {
    var bars = document.getElementById('bars');
    var chips = document.getElementById('sym-top');
    if (!bars) return;

    // 1. Batang tumbuh dari 0
    ambilBatang(bars).forEach(function (el, i) {
      var lebar = el.dataset.lebar || el.style.width;
      el.dataset.lebar = lebar;
      el.style.transition = 'none';
      el.style.width = '0%';
      void el.offsetWidth;
      el.style.transition = 'width ' + DURASI + 'ms cubic-bezier(.22,1,.36,1) ' + (i * JEDA_BARIS) + 'ms';
      el.style.width = lebar;
    });

    // 2. Angka bergulir dari 0
    ambilAngka(bars).forEach(function (el, i) {
      var nilai = parseInt(el.dataset.nilai || el.textContent, 10);
      el.dataset.nilai = nilai;
      hitung(el, nilai, i * JEDA_BARIS);
    });

    // 3. Label gejala terbanyak muncul satu per satu
    if (chips) {
      Array.prototype.forEach.call(chips.children, function (el, i) {
        el.style.transition = 'none';
        el.style.opacity = '0';
        el.style.transform = 'scale(.7)';
        void el.offsetWidth;
        var d = 600 + i * 150;
        el.style.transition = 'opacity .4s ease ' + d + 'ms, transform .4s cubic-bezier(.34,1.56,.64,1) ' + d + 'ms';
        el.style.opacity = '1';
        el.style.transform = 'scale(1)';
      });
    }
  }

  function init(coba) {
    var bars = document.getElementById('bars');
    // tunggu sampai index.js selesai mengisi grafik
    if (!bars || !bars.children.length) {
      if ((coba || 0) < 50) setTimeout(function () { init((coba || 0) + 1); }, 100);
      return;
    }
    var kartu = bars.closest('.card') || bars;
    if (!('IntersectionObserver' in window)) return;

    new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) jalankan();   // jalan tiap kali kartu terlihat
      });
    }, { threshold: 0.3 }).observe(kartu);
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function () { init(0); });
  else init(0);
})();