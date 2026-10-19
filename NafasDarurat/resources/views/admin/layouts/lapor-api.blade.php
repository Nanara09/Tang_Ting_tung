@extends('layouts')
@section('title', 'Lapor titik api | Nafas Darurat')
@section('description', 'Laporkan titik api atau asap tebal lengkap dengan lokasi dan foto. Laporan ditinjau admin sebelum tampil di peta.')
@section('leaflet', true)

@section('content')
<div class="page-head">
  <div class="container">
    <a class="back" href="{{ route('home') }}#peta" aria-label="Kembali ke peta titik api">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 5l-7 7 7 7" stroke="#14302B" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
    </a>
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
          <input id="place" type="text" maxlength="80" placeholder="Contoh: Kel. A, Palangka Raya" autocomplete="off" required>
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

    <div class="notice">
      <span>Nama dan kontakmu tidak ditampilkan ke publik. Laporan ditinjau admin sebelum muncul di peta. Foto diperkecil dan data lokasi bawaan foto (EXIF) dihapus.</span>
    </div>

    <p class="err" id="err" role="alert"></p>
    <button type="submit" class="btn btn-fire btn-block">Kirim laporan</button>
  </form>
</main>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/lapor-api.js') }}"></script>
@endpush
