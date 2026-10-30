@extends('layouts')
@section('title', 'Cek kesehatan | Nafas Darurat')
@section('description', 'Pilih gejala yang kamu rasakan setelah terpapar asap dan dapatkan tingkat risiko serta saran tindakan.')

@section('content')
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cek kesehatan | NafasAman</title>
<meta name="description" content="Pilih gejala yang kamu rasakan setelah terpapar asap dan dapatkan tingkat risiko serta saran tindakan.">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}">
</head>
<body>

<div class="page-head">
    <div class="container">
        <h1>Cek kesehatan</h1>
    </div>
</div>

<main id="main" class="form-page">
    <form id="cek-form" style="display:flex;flex-direction:column;gap:22px" novalidate>
        <div class="field">
            <label for="kel"><h2 style="margin:0">Nama</h2></label>
            <input id="kel" name="kel" type="text" maxlength="40" placeholder="Masukkan Nama Anda" autocomplete="off" required>
        </div>

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
            <span class="chip lg lv-none" id="res-chip">
                <i class="dot"></i><span id="res-label">Belum ada gejala dipilih</span>
            </span>
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
<script src="{{ asset('assets/js/cek.js') }}"></script>
<script src="{{ asset('public/assets/js/data.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/reveal.js') }}"></script>
<script src="{{ asset('public/assets/js/index.js') }}"></script>
<script src="{{ asset('public/assets/js/cek.js') }}"></script>
<script src="{{ asset('public/assets/js/lapor-gejala.js') }}"></script>
<script src="{{ asset('public/assets/js/lapor-api.js') }}"></script>
<script src="{{ asset('public/assets/js/admin.js') }}"></script>    
@endpush
