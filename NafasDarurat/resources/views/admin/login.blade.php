@extends('layouts')
@section('title', 'Masuk admin | NafasDarurat')
@section('robots')
<meta name="robots" content="noindex">
@endsection

@section('content')
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
@endsection
