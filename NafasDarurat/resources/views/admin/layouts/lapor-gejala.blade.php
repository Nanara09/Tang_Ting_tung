@extends('layouts')
@section('title', 'Lapor gejala | Nafas Darurat')
@section('description', 'Laporkan gejala kesehatan akibat kabut asap secara anonim. Data dirangkum per wilayah.')

@section('content')
<div class="page-head">
  <div class="container">
    <a class="back" href="{{ route('home') }}" aria-label="Kembali ke beranda">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 5l-7 7 7 7" stroke="#14302B" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
    </a>
    <h1>Lapor gejala</h1>
  </div>
</div>

<main id="main" class="form-page">
  <div id="done" class="notice success" role="status" hidden>
    <span>Terima kasih, laporanmu sudah tercatat dan masuk ke ringkasan per wilayah. <a href="{{ route('home') }}#kesehatan"><b>Lihat ringkasan</b></a></span>
  </div>

  <form id="form" novalidate style="display:flex;flex-direction:column;gap:22px">
    <section aria-labelledby="h1">
      <h2 id="h1"><span class="n"></span>Wilayah</h2>
      <div class="fields">
        <div class="field">
          <label for="kota">Kota</label>
          <input id="kota" name="kota" type="text" list="kota-list" maxlength="40" autocomplete="off" required>
          <datalist id="kota-list"></datalist>
        </div>
        <div class="field">
          <label for="kel">Kelurahan / desa</label>
          <input id="kel" name="kel" type="text" maxlength="40" placeholder="Contoh: Kelurahan A" autocomplete="off" required>
        </div>
      </div>
       <div class="field">
          <label for="nama">Nama Pengguna</label>
          <input id="nama" name="nama" type="text" maxlength="40" placeholder="Contoh: Asep" autocomplete="off" required>
        </div>
      </div>
    </section>

    <section aria-labelledby="h2">
      <h2 id="h2"><span class="n"></span>Gejala yang dirasakan</h2>
      <div class="check-grid" id="sym-grid"></div>
    </section>

    <section aria-labelledby="h3">
      <h2 id="h3"><span class="n"></span>Sudah berapa lama</h2>
      <div class="opts" id="dur-opts"></div>
    </section>

    <section aria-labelledby="h4">
      <h2 id="h4"><span class="n"></span>Kelompok pelapor</h2>
      <div class="opts" id="group-opts"></div>
    </section>

    <p class="err" id="err" role="alert"></p>
    <button type="submit" class="btn btn-primary btn-block">Kirim laporan gejala</button>
  </form>
</main>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/lapor-gejala.js') }}"></script>
@endpush
