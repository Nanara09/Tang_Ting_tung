@extends('layouts')
@section('title', 'Cek kesehatan | Nafas Darurat')
@section('description', 'Pilih gejala yang kamu rasakan setelah terpapar asap dan dapatkan tingkat risiko serta saran tindakan.')

@section('content')
<div class="page-head">
  <div class="container">
    <a class="back" href="{{ route('home') }}" aria-label="Kembali ke beranda">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 5l-7 7 7 7" stroke="#14302B" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
    </a>
    <h1>Cek kesehatan</h1>
  </div>
</div>

<main id="main" class="form-page">
  <p class="muted" style="margin:0">Pilih gejala yang kamu rasakan setelah terpapar asap. Hasilnya panduan awal, bukan diagnosis.</p>

  <form id="cek-form" style="display:flex;flex-direction:column;gap:22px" novalidate>
    <section aria-labelledby="h-gejala">
      <h2 id="h-gejala">Gejala yang dirasakan</h2>
      <div class="check-grid" id="sym-grid"></div>
    </section>
    <section aria-labelledby="h-kel">
      <h2 id="h-kel">Hasil untuk</h2>
      <div class="opts" id="group-opts"></div>
    </section>
  </form>

  <section class="result-card" id="result" aria-live="polite">
    <div class="result-top">
      <span class="small muted" id="count">0 gejala dipilih</span>
      <span class="chip lg lv-none" id="res-chip"><i class="dot"></i><span id="res-label">Belum ada gejala dipilih</span></span>
    </div>
    <ul class="tips" id="res-tips"></ul>
    <p class="small muted" style="margin:0">Hasil ini hanya panduan awal, bukan diagnosis. Jika ragu, hubungi tenaga kesehatan.</p>
  </section>

  <div class="stack" style="gap:8px">
    <a class="btn btn-primary btn-block" id="to-report" href="{{ route('lapor.gejala') }}">Laporkan gejala ini</a>
    <a class="btn btn-outline" style="min-height:48px;border-radius:16px" href="{{ route('home') }}#fasilitas">Cari fasilitas kesehatan</a>
  </div>
</main>
@endsection

@push('scripts')
@vite('resources/js/cek.js')
@endpush
