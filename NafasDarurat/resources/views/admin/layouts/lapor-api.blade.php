@extends('layouts')
@section('title', 'Lapor titik api | Nafas Darurat')
@section('description', 'Laporkan titik api atau asap tebal lengkap dengan lokasi dan foto. Laporan ditinjau admin sebelum tampil di peta.')
@section('leaflet', true)

@section('content')
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lapor titik api | NafasAman</title>
<meta name="description" content="Laporkan titik api atau asap tebal lengkap dengan lokasi dan foto. Laporan ditinjau admin sebelum tampil di peta.">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}">
</head>
<body>
<div class="page-head">
  <div class="container">
    <h1>Lapor titik api</h1>
  </div>
</div>

<main id="main" class="form-page">
  <div id="done" class="notice success" role="status" hidden>
    <span>Laporan terkirim dan menunggu verifikasi admin. Setelah disetujui, laporan tampil di peta. <a href="{{ route('home') }}#peta"><b>Kembali ke peta</b></a></span>
  </div>

  <form id="form" novalidate style="display:flex;flex-direction:column;gap:22px">
    <section aria-labelledby="h1">
      <h2 id="h1"><span class="n">1</span>Tandai lokasi</h2>
      <div class="stack">
        <div class="field">
          <label for="place">Nama lokasi</label>
          <input id="place" type="text" maxlength="80" placeholder="Contoh: Kel Jatihandap" autocomplete="off" required>
        </div>
        <div class="pick-map"><div id="pick-map" style="position:absolute;inset:0" aria-label="Peta untuk menandai lokasi api"></div></div>
        <button type="button" class="btn btn-outline" id="btn-gps" style="border-radius:14px">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="5.5" stroke="currentColor" stroke-width="1.8"></circle><circle cx="10" cy="10" r="1.8" fill="currentColor"></circle><path d="M10 1.5v3M10 15.5v3M1.5 10h3M15.5 10h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path></svg>
          Gunakan lokasi saya
        </button>
        <div class="coords" id="coords">Ketuk peta atau geser pin ke titik api.</div>
      </div>
    </section>

    <section aria-labelledby="h2">
      <h2 id="h2"><span class="n">2</span>Kondisi api sekarang</h2>
      <div class="opts col" id="status-opts"></div>
    </section>

    <section aria-labelledby="h3">
      <h2 id="h3"><span class="n">3</span>Foto <span class="small muted" style="font-weight:500">(opsional)</span></h2>
      <label class="drop" for="foto">
        <strong>Ketuk untuk unggah foto</strong>
        <span class="small muted">JPG atau PNG, maksimal 2 MB</span>
        <input id="foto" type="file" accept="image/jpeg,image/png">
      </label>
      <div class="preview" id="preview"><img id="preview-img" alt="Pratinjau foto laporan"></div>
    </section>

    <section aria-labelledby="h4">
      <h2 id="h4"><span class="n">4</span>Keterangan</h2>
      <div class="field">
        <label class="visually-hidden" for="desc">Keterangan</label>
        <textarea id="desc" maxlength="300" rows="3" placeholder="Contoh: api terlihat di lahan kosong dekat permukiman"></textarea>
      </div>
    </section>

    <p class="err" id="err" role="alert"></p>
    <button type="submit" class="btn btn-fire btn-block">Kirim laporan</button>
  </form>
</main>




@endsection

@push('scripts')
<script src="{{ asset('public/assets/js/data.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/reveal.js') }}"></script>
<script src="{{ asset('public/assets/js/index.js') }}"></script>
<script src="{{ asset('public/assets/js/cek.js') }}"></script>
<script src="{{ asset('public/assets/js/lapor-gejala.js') }}"></script>
<script src="{{ asset('public/assets/js/lapor-api.js') }}"></script>
<script src="{{ asset('public/assets/js/admin.js') }}"></script>
</body>
</html>
@endpush
