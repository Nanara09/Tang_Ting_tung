@extends('layouts')
@section('title', 'Masuk admin | NafasDarurat')
@section('robots')
<meta name="robots" content="noindex">
@endsection

@section('content')
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lapor gejala | NafasDarurat</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}">
</head>
<main id="main" class="container" style="padding-top:32px">
  <section class="gate card">
    <h1 style="margin:0 0 8px;font-size:24px;font-weight:800">Masuk admin</h1>
    <p class="muted small">Khusus pengelola NafasDarurat.</p>
    <form method="POST" action="{{ route('admin.login.submit') }}" novalidate>
      @csrf
      <div class="field">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required>
      </div>
      <div class="field">
        <label for="password">Kata sandi</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
      </div>
      @if ($errors->any())
        <p class="err" role="alert">{{ $errors->first() }}</p>
      @endif
      <button type="submit" class="btn btn-primary btn-block">Masuk</button>
    </form>
  </section>
</main>

<script src="{{ asset('public/assets/js/data.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/reveal.js') }}"></script>
<script src="{{ asset('public/assets/js/index.js') }}"></script>
<script src="{{ asset('public/assets/js/cek.js') }}"></script>
<script src="{{ asset('public/assets/js/lapor-gejala.js') }}"></script>
<script src="{{ asset('public/assets/js/lapor-api.js') }}"></script>
<script src="{{ asset('public/assets/js/admin.js') }}"></script>
@endsection
