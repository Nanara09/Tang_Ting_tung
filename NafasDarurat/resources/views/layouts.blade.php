<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'NafasDarurat | Pantau udara, jaga napas keluarga')</title>
    <meta name="description" content="@yield('description', 'Cek kualitas udara, periksa gejala kesehatan akibat kabut asap, dan laporkan keluhan serta titik api.')">

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Leaflet (hanya dimuat di halaman yang butuh peta) --}}
    @hasSection('leaflet')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    @endif

    {{-- CSS utama: public/assets/css/style.css --}}
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @stack('styles')
</head>
<body>
    <a class="skip" href="#main">Lewati ke konten</a>

    <header id="site-header" class="site-header"></header>

    @yield('content')

    <footer id="site-footer" class="site-footer"></footer>

    {{-- Library dimuat lebih dulu --}}
    @hasSection('leaflet')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @endif

    {{-- URL halaman untuk navbar & footer (dipakai common.js) --}}
    @php
        $navRoutes = [
            'home'        => Route::has('home')         ? route('home')         : url('/'),
            'cek'         => Route::has('cek')          ? route('cek')          : url('/cek'),
            'laporApi'    => Route::has('lapor.api')    ? route('lapor.api')    : url('/lapor-api'),
            'laporGejala' => Route::has('lapor.gejala') ? route('lapor.gejala') : url('/lapor-gejala'),
            'admin'       => Route::has('login')        ? route('login')        : url('/login'),
        ];
    @endphp
    <script>window.ROUTES = @json($navRoutes);</script>

    {{-- Script bersama untuk semua halaman (urutan penting) --}}
    <script src="{{ asset('assets/js/data.js') }}"></script>
    <script src="{{ asset('assets/js/common.js') }}"></script>
    <script src="{{ asset('assets/js/reveal.js') }}"></script>

    {{-- Script khusus per halaman --}}
    @stack('scripts')
</body>
</html>