/* NafasDarurat - common.js
   Menyisipkan header dan footer yang sama di semua halaman.
   URL halaman diambil dari window.ROUTES (diisi di layouts.blade.php). */
(function () {
  "use strict";

  /* ---------- URL halaman dari Laravel ---------- */
  const R = Object.assign(
    {
      home: "/",
      cek: "/cek.blade.php",
      laporApi: "/lapor-api",
      laporGejala: "/lapor-gejala",
      admin: "/login",
    },
    window.ROUTES || {}
  );

  // Buang garis miring di akhir supaya "/cek/" dan "/cek" dianggap sama
  function cleanPath(url) {
    const p = new URL(url, location.origin).pathname.replace(/\/+$/, "");
    return p || "/";
  }
  const here = cleanPath(location.href);
  const isCurrent = (url) => cleanPath(url) === here;
  const homeAnchor = (id) => R.home.replace(/\/+$/, "") + "/#" + id;

  const LOGO =
    '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">' +
    '<circle cx="16" cy="16" r="15" stroke="#0E6B5C" stroke-width="2"></circle>' +
    '<path d="M6 12h13a3 3 0 1 0-3-3" stroke="#0B3D36" stroke-width="2.2" stroke-linecap="round"></path>' +
    '<path d="M6 17h18a3 3 0 1 1-3 3" stroke="#0B3D36" stroke-width="2.2" stroke-linecap="round"></path>' +
    '<path d="M6 22h9" stroke="#0E6B5C" stroke-width="2.2" stroke-linecap="round"></path></svg>';

  // Tombol hamburger (hanya terlihat di HP, diatur oleh CSS)
  const TOGGLE =
    '<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="Buka menu">' +
    '<svg class="ic-open" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>' +
    '<svg class="ic-close" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>' +
    "</button>";

  // markActive: true = beri tanda halaman aktif (aria-current)
  const LINKS = [
    { href: homeAnchor("main"), label: "Beranda" },
    { href: homeAnchor("peta"), label: "Peta titik api" },
    { href: homeAnchor("fasilitas"), label: "Fasilitas" },
    { href: R.laporApi, label: "Lapor Titik Api", markActive: true },
    { href: R.laporGejala, label: "Lapor gejala", markActive: true },
  ];

  /* ---------- Header ---------- */
  const header = document.getElementById("site-header");
  if (header) {
    const links = LINKS.map(
      (l) =>
        '<a href="' + l.href + '"' +
        (l.markActive && isCurrent(l.href) ? ' aria-current="page"' : "") +
        ">" + l.label + "</a>"
    ).join("");

    header.innerHTML =
      '<div class="container header-inner">' +
      '<a class="brand" href="' + R.home + '">' + LOGO + "<span>NafasDarurat</span></a>" +
      TOGGLE +
      '<nav class="nav" id="site-nav" aria-label="Menu utama">' +
      links +
      '<a class="nav-cta" href="' + R.cek + '"' +
      (isCurrent(R.cek) ? ' aria-current="page"' : "") +
      ">Cek kesehatan</a>" +
      "</nav></div>";

    /* ---------- Buka/tutup menu di HP ---------- */
    const toggle = header.querySelector(".nav-toggle");

    function setOpen(open) {
      header.classList.toggle("is-open", open);
      toggle.setAttribute("aria-expanded", open);
      toggle.setAttribute("aria-label", open ? "Tutup menu" : "Buka menu");
    }

    toggle.addEventListener("click", () => setOpen(!header.classList.contains("is-open")));
    // tutup saat pilih menu, tekan Esc, atau tap di luar navbar
    header.querySelectorAll(".nav a").forEach((a) => a.addEventListener("click", () => setOpen(false)));
    document.addEventListener("keydown", (e) => { if (e.key === "Escape") setOpen(false); });
    document.addEventListener("click", (e) => { if (!header.contains(e.target)) setOpen(false); });
  }

  /* ---------- Footer ---------- */
  const footer = document.getElementById("site-footer");
  if (footer) {
    const year = new Date().getFullYear();
    footer.innerHTML =
      '<div class="container footer-grid">' +
        '<div class="footer-brand">' +
          '<a class="brand" href="' + R.home + '">' + LOGO + "<span>NafasDarurat</span></a>" +
          "<p>Pantau kualitas udara, cek kesehatan, dan laporkan titik api untuk membantu warga terdampak kabut asap.</p>" +
        "</div>" +
        '<div><h3>Menu</h3><ul>' +
          '<li><a href="' + homeAnchor("main") + '">Beranda</a></li>' +
          '<li><a href="' + homeAnchor("peta") + '">Peta titik api</a></li>' +
          '<li><a href="' + homeAnchor("fasilitas") + '">Fasilitas</a></li>' +
          '<li><a href="' + R.cek + '">Cek kesehatan</a></li>' +
        "</ul></div>" +
        '<div><h3>Lapor</h3><ul>' +
          '<li><a href="' + R.laporApi + '">Lapor titik api</a></li>' +
          '<li><a href="' + R.laporGejala + '">Lapor gejala</a></li>' +
        "</ul></div>" +
        '<div><h3>Kontak darurat</h3><ul>' +
          "<li>Posko Karhutla: <br>0813-1003-5000</li>" +
          "<li>Darurat umum: 112</li>" +
          "<li>Ambulans: 119</li>" +
          "<li>Pemadam kebakaran: 113</li>" +
          "<li>nafas.darurat@gmail.com</li>" +
        "</ul></div>" +
      "</div>" +
      '<div class="container footer-bottom">' +
        "<span>&copy; " + year + " NafasDarurat &middot; Sistem Pemantauan Karhutla</span>" +
        '<a href="' + R.admin + '">Admin</a>' +
      "</div>";
  }
})();