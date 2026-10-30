@extends('layouts')
@section('title', 'Lapor gejala | Nafas Darurat')
@section('description', 'Laporkan gejala kesehatan akibat kabut asap secara anonim. Data dirangkum per wilayah.')

@section('content')
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lapor gejala | NafasAman</title>
<meta name="description" content="Laporkan gejala kesehatan akibat kabut asap secara anonim. Data dirangkum per wilayah.">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}">
</head>
<body>
<div class="page-head">
  <div class="container">
    <h1>Lapor gejala</h1>
  </div>
</div>

<main id="main" class="form-page">
  <div id="done" class="notice success" role="status" hidden>
    <span>Terima kasih, laporanmu sudah tercatat dan masuk ke ringkasan per wilayah. <a href="index.html#kesehatan"><b>Lihat ringkasan</b></a></span>
  </div>

  <form id="form" novalidate style="display:flex;flex-direction:column;gap:22px">
    <section aria-labelledby="h1">
      <h2 id="h1"><span class="n">1</span>Wilayah</h2>
      <div class="fields">
        <div class="field">
          <label for="kota">Kota / Kabupaten</label>
          <input id="kota" name="kota" type="text" placeholder="Contoh: Kota Bandung" list="kota-list" maxlength="40" autocomplete="off" required>
          <datalist id="kota-list"></datalist>
        </div>
        <div class="field">
          <label for="kel">Kelurahan / desa</label>
          <input id="kel" name="kel" type="text" maxlength="40" placeholder="Contoh: Kelurahan Jatihandap" autocomplete="off" required>
        </div>
      </div>
    </section>

    <section aria-labelledby="h2">
      <h2 id="h2"><span class="n">2</span>Gejala yang dirasakan</h2>
      <div class="check-grid" id="sym-grid"></div>
    </section>

    <section aria-labelledby="h3">
      <h2 id="h3"><span class="n">3</span>Sudah berapa lama</h2>
      <div class="opts" id="dur-opts"></div>
    </section>

    <section aria-labelledby="h4">
      <h2 id="h4"><span class="n">4</span>Kelompok pelapor</h2>
      <div class="opts" id="group-opts"></div>
    </section>

    <p class="err" id="err" role="alert"></p>
    <button type="submit" class="btn btn-primary btn-block">Kirim laporan gejala</button>
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
