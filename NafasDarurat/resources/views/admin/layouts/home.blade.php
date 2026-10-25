@extends('layouts')
@section('title', 'Nafas Darurat | Pantau udara, jaga napas keluarga')
@section('description', 'Cek kualitas udara, periksa gejala kesehatan akibat kabut asap, dan laporkan keluhan serta titik api.')
@section('leaflet', true)

@section('content')
<main id="main">
  <section class="hero">
    <div class="container hero-inner">
      <div class="hero-copy">
        <span class="eyebrow">Pantauan kabut asap</span>
        <h1>Pantau udara, jaga napas keluarga.</h1>
        <p class="lead">Cek kualitas udara, periksa gejala kesehatanmu, dan laporkan keluhan supaya petugas tahu wilayah mana yang paling terdampak.</p>
        <div class="row">
          <a class="btn btn-light" href="{{ route('cek') }}">Cek kesehatanmu</a>
          <a class="btn btn-ghost-light" href="{{ route('lapor.gejala') }}">Lapor gejala</a>
        </div>
      </div>

      <div class="air" id="cek">
        <h2>Cek udara &amp; saran kesehatan</h2>
        <div>
          <span class="label" id="lbl-city">Pilih kota</span>
          <div class="row" id="city-pills" role="group" aria-labelledby="lbl-city"></div>
        </div>
        <div class="air-result lv-none" id="air-result" aria-live="polite">
          <div>
            <div class="muted small" id="air-city" style="font-weight:600">&nbsp;</div>
            <div class="air-num"><strong id="air-ispu">-</strong><span class="label" style="margin:0">ISPU</span></div>
          </div>
          <span class="chip lg" id="air-level"></span>
        </div>
        <div>
          <span class="label" id="lbl-group">Saran untuk</span>
          <div class="row" id="group-pills" role="group" aria-labelledby="lbl-group"></div>
        </div>
        <ul class="tips" id="air-tips"></ul>
        <p class="small muted" id="air-source" style="margin:0"></p>
      </div>
    </div>
  </section>

  <section class="section container" id="kesehatan">
    <h2 class="title">Cek kesehatan &amp; lapor gejala</h2>
    <p class="sub">Merasakan keluhan akibat asap? Periksa gejalamu, lalu laporkan supaya petugas tahu wilayah mana yang paling terdampak.</p>
    <div class="grid-2">
      <article class="card stack">
        <span class="icon-badge" style="background:#DDF3E6">
          <svg width="26" height="26" viewBox="0 0 26 26" fill="none" aria-hidden="true"><path d="M3 13h5l2.5-5 4 10 2.5-5H23" stroke="#17623A" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
        </span>
        <h3 style="margin:4px 0 0;font-size:20px;font-weight:800">Cek kesehatan</h3>
        <p class="muted" style="margin:0">Pilih gejala yang kamu rasakan, lalu dapatkan tingkat risiko dan saran tindakan sesuai kelompokmu.</p>
        <a class="btn btn-primary" style="align-self:flex-start" href="{{ route('cek') }}">Mulai cek</a>
      </article>
      <article class="card stack">
        <span class="icon-badge" style="background:#DCEBFA">
          <svg width="26" height="26" viewBox="0 0 26 26" fill="none" aria-hidden="true"><rect x="5" y="4" width="16" height="19" rx="3" stroke="#1D4E89" stroke-width="2"></rect><path d="M9 4.5h8M9 11h8M9 15h8M9 19h4" stroke="#1D4E89" stroke-width="2" stroke-linecap="round"></path></svg>
        </span>
        <h3 style="margin:4px 0 0;font-size:20px;font-weight:800">Lapor gejala</h3>
        <p class="muted" style="margin:0">Laporkan keluhanmu secara anonim. Data dirangkum per wilayah, tanpa nama pelapor.</p>
        <a class="btn btn-outline" style="align-self:flex-start" href="{{ route('lapor.gejala') }}">Lapor gejala</a>
      </article>
    </div>

    <div class="card stack" style="margin-top:20px;gap:18px">
      <div class="row" style="align-items:center;justify-content:space-between">
        <h3 style="margin:0;font-size:18px;font-weight:800">Gejala yang dilaporkan per wilayah</h3>
        <span class="chip neutral">Data contoh + laporan baru dari browser ini</span>
      </div>
      <div class="bars" id="bars"></div>
      <div class="row small muted" style="align-items:center">
        <b>Gejala terbanyak:</b>
        <span id="sym-top" class="row"></span>
      </div>
    </div>
  </section>

  <section class="section container" id="peta">
    <div class="row" style="justify-content:space-between;align-items:flex-end;margin-bottom:24px;gap:16px">
      <div style="max-width:560px">
        <h2 class="title">Peta titik api</h2>
        <p class="sub" style="margin-bottom:12px">Penyebab asap di sekitarmu: laporan titik api dari warga, dikonfirmasi bersama supaya tahu api masih menyala atau sudah padam.</p>
        <a class="btn btn-fire" href="{{ route('lapor.api') }}">Lapor titik api</a>
      </div>
      <div class="row" id="filter-pills" role="group" aria-label="Filter status"></div>
    </div>
    <div class="map-layout">
      <div class="map-wrap">
        <div id="map" aria-label="Peta laporan titik api"></div>
        <div class="legend" aria-hidden="true">
          <span><i style="background:#D92D20"></i>Masih terbakar</span>
          <span><i style="background:#E8920B"></i>Dalam penanganan</span>
          <span><i style="background:#1E8A4C"></i>Sudah padam</span>
        </div>
      </div>
      <div class="reports" id="reports" aria-live="polite"></div>
    </div>
  </section>

  <section class="section container" id="fasilitas">
    <h2 class="title">Rumah oksigen &amp; posko kesehatan</h2>
    <p class="sub">Temukan layanan terdekat untuk warga yang sesak napas akibat asap. Data dikelola admin; isian di bawah masih contoh.</p>
    <div class="toolbar">
      <div class="search">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="9" cy="9" r="6" stroke="#4A625D" stroke-width="1.8"></circle><path d="M14 14l4 4" stroke="#4A625D" stroke-width="1.8" stroke-linecap="round"></path></svg>
        <label class="visually-hidden" for="fac-q">Cari kota atau nama fasilitas</label>
        <input id="fac-q" type="search" placeholder="Cari kota atau nama fasilitas" autocomplete="off">
      </div>
      <div class="row" id="fac-types" role="group" aria-label="Jenis fasilitas"></div>
    </div>
    <div class="grid-2" id="fac-list" aria-live="polite"></div>
  </section>

  <section class="section container">
    <div class="cta">
      <div style="flex:1 1 340px;min-width:0">
        <h2>Batuk, sesak, atau mata perih karena asap? Cek kesehatanmu sekarang.</h2>
        <p>Melihat api atau asap tebal? Lapor titik api di sini, atau hubungi Posko Karhutla Kementerian Kehutanan di +62 8131-00-35000 dan 021-5704618.</p>
      </div>
      <div class="row" style="gap:12px">
        <a class="btn btn-primary" href="{{ route('cek') }}">Cek kesehatan</a>
        <a class="btn btn-fire-outline" href="{{ route('lapor.api') }}">Lapor titik api</a>
      </div>
    </div>
  </section>
</main>

<a class="btn btn-primary "href="admin/masuk">Admin</a>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/index.js') }}"></script>
@endpush
