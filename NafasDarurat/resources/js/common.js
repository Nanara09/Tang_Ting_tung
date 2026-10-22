/* NafasDarurat - common.js
   Menyisipkan header dan footer yang sama di semua halaman. */
(function () {
  'use strict';

  const LOGO =
    '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">' +
    '<circle cx="16" cy="16" r="15" stroke="#7FD1BE" stroke-width="2"></circle>' +
    '<path d="M6 12h13a3 3 0 1 0-3-3" stroke="#fff" stroke-width="2.2" stroke-linecap="round"></path>' +
    '<path d="M6 17h18a3 3 0 1 1-3 3" stroke="#fff" stroke-width="2.2" stroke-linecap="round"></path>' +
    '<path d="M6 22h9" stroke="#7FD1BE" stroke-width="2.2" stroke-linecap="round"></path></svg>';

  const LINKS = [
    { href: 'cek.blade.php#cek', label: 'Cek udara', page: '' },
    { href: 'lapor-gejala.blade.php', label: 'Lapor gejala', page: 'lapor-gejala.blade.php' },
    { href: 'home.blade.php#peta', label: 'Peta titik api', page: '' },
    { href: 'home.blade.php#fasilitas', label: 'Fasilitas', page: '' }
  ];

  const here = location.pathname.split('/').pop() || 'home.blade.php';

  const header = document.getElementById('site-header');
  if (header) {
    const links = LINKS.map(
      (l) => '<a href="' + l.href + '"' + (l.page && l.page === here ? ' aria-current="page"' : '') + '>' + l.label + '</a>'
    ).join('');
    header.innerHTML =
      '<div class="container header-inner">' +
      '<a class="brand" href="home.blade.php">' + LOGO + '<span>NafasDarurat</span></a>' +
      '<nav class="nav" aria-label="Menu utama">' + links +
      '<a class="nav-cta" href="cek.html"' + (here === 'cek.blade.php' ? ' aria-current="page"' : '') + '>Cek kesehatan</a>' +
      '</nav></div>';
  }

  const footer = document.getElementById('site-footer');
  if (footer) {
    footer.innerHTML =
      '<div class="container footer-inner">' +
      '<span>NafasDarurat &middot; prototipe untuk UINIC 8.0, subtema Kesehatan &middot; <a href="home.blade.php#fasilitas">Admin</a></span>' +
      '<span>Peta &copy; OpenStreetMap contributors &middot; Data laporan pada prototipe ini adalah data contoh</span>' +
      '</div>';
  }
})();
