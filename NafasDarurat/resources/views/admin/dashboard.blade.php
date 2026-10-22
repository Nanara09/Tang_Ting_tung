@extends('layouts')
@section('title', 'Admin | NafasDarurat')
@section('robots')
<meta name="robots" content="noindex">
@endsection

@php
    $stateInfo = [
        'pending' => ['Menunggu', 'lv-sedang'],
        'approved' => ['Disetujui', 'lv-baik'],
        'rejected' => ['Ditolak', 'lv-sangat-buruk'],
    ];
    $durasi = collect(config('nafasdarurat.durations'))->pluck('name', 'id');
    $kelompok = collect(config('nafasdarurat.groups'))->pluck('name', 'id');
    $statusLabel = config('nafasdarurat.status');
@endphp

@section('content')
<main id="main" class="container" style="padding-top:32px">
  <div class="row" style="justify-content:space-between;align-items:flex-start;margin-bottom:24px;gap:16px">
    <div style="max-width:600px">
      <h1 style="margin:0 0 6px;font-size:32px;line-height:1.15;font-weight:800;letter-spacing:-0.01em">Verifikasi laporan</h1>
      <p class="muted" style="margin:0">Tinjau laporan titik api sebelum tampil di peta publik. Laporan gejala masuk otomatis dan dirangkum per wilayah.</p>
    </div>
    <form method="POST" action="{{ route('admin.logout') }}">
      @csrf
      <button type="submit" class="btn btn-outline">Keluar</button>
    </form>
  </div>

  <div class="stats">
    @foreach ($stats as [$label, $n])
      <div class="stat"><span>{{ $label }}</span><strong>{{ $n }}</strong></div>
    @endforeach
  </div>

  <div class="tabs" role="tablist" aria-label="Bagian admin">
    <button type="button" class="tab" role="tab" id="tab-fire" aria-selected="{{ $tab === 'fire' ? 'true' : 'false' }}" aria-controls="pane-fire">Titik api</button>
    <button type="button" class="tab" role="tab" id="tab-health" aria-selected="{{ $tab === 'health' ? 'true' : 'false' }}" aria-controls="pane-health">Gejala warga</button>
  </div>

  <div id="pane-fire" role="tabpanel" aria-labelledby="tab-fire" @if($tab !== 'fire') hidden @endif>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Foto</th><th>Lokasi dan keterangan</th><th>Waktu</th><th>Status dilaporkan</th><th>Verifikasi</th><th>Tindakan</th></tr></thead>
        <tbody>
          @forelse ($fire as $r)
            <tr>
              <td>
                @if ($r->photo_path)
                  <img class="thumb" src="{{ $r->photoUrl() }}" alt="Foto laporan {{ $r->place }}">
                @else
                  <span class="thumb" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <rect x="3" y="5" width="18" height="14" rx="2.5" stroke="currentColor" stroke-width="1.8">
                  </rect>
                  <circle cx="9" cy="10.5" r="1.8" stroke="currentColor" stroke-width="1.6">
                  </circle>
                  <path d="M4 17l5-4.5 3.5 3L16 12l4 4" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round">
                  </path>
                  </svg>
                  </span>
                @endif
              </td>
              <td>
                <b>{{ $r->place }}</b><br>
                <span class="muted">{{ $r->description ?: '-' }}</span><br>
                <span class="small muted">{{ number_format($r->lat, 4) }}, {{ number_format($r->lng, 4) }}</span>
              </td>
              <td>{{ $r->created_at->diffForHumans() }}</td>
              <td><span class="chip st-{{ $r->status }}"><i class="dot"></i>{{ $statusLabel[$r->status] ?? $r->status }}</span></td>
              <td><span class="chip {{ $stateInfo[$r->state][1] }}">{{ $stateInfo[$r->state][0] }}</span></td>
              <td>
                <div class="row">
                  @if ($r->state !== 'approved')
                    <form method="POST" action="{{ route('admin.fire.update', $r) }}">
                      @csrf @method('PATCH')
                      <input type="hidden" name="state" value="approved">
                      <button type="submit" class="btn-sm ok" aria-label="Setujui laporan {{ $r->place }}">Setujui</button>
                    </form>
                  @endif
                  @if ($r->state !== 'rejected')
                    <form method="POST" action="{{ route('admin.fire.update', $r) }}">
                      @csrf @method('PATCH')
                      <input type="hidden" name="state" value="rejected">
                      <button type="submit" class="btn-sm bad" aria-label="Tolak laporan {{ $r->place }}">Tolak</button>
                    </form>
                  @endif
                  <form method="POST" action="{{ route('admin.fire.destroy', $r) }}" onsubmit="return confirm('Hapus laporan ini secara permanen?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-sm" aria-label="Hapus laporan {{ $r->place }}">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="muted">Belum ada laporan titik api.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <p class="small muted">Nama dan kontak pelapor tidak disimpan, sehingga tidak ada data pribadi di tabel ini.</p>
  </div>

  <div id="pane-health" role="tabpanel" aria-labelledby="tab-health" @if($tab !== 'health') hidden @endif>
    <div class="row" style="justify-content:space-between;align-items:center;margin-bottom:12px">
      <h2 style="margin:0;font-size:18px;font-weight:800">Ringkasan per wilayah</h2>
      <a class="btn btn-primary" href="{{ route('admin.health.csv') }}">Unduh CSV</a>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Wilayah</th><th>Jumlah laporan</th><th>Gejala terbanyak</th></tr></thead>
        <tbody>
          @forelse ($wilayah as $w)
            <tr><td><b>{{ $w['name'] }}</b></td><td>{{ $w['n'] }}</td><td>{{ $w['top'] }}</td></tr>
          @empty
            <tr><td colspan="3" class="muted">Belum ada laporan gejala.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <h2 style="margin:28px 0 12px;font-size:18px;font-weight:800">10 laporan terbaru</h2>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Waktu</th><th>Wilayah</th><th>Gejala</th><th>Lama</th><th>Kelompok</th></tr></thead>
        <tbody>
          @foreach ($recent as $r)
            <tr>
              <td>{{ $r->created_at->diffForHumans() }}</td>
              <td>{{ $r->kelurahan }}, {{ $r->kota }}</td>
              <td>{{ collect($r->gejala)->map(fn ($id) => $labels[$id] ?? $id)->implode(', ') }}</td>
              <td>{{ $durasi[$r->durasi] ?? $r->durasi }}</td>
              <td>{{ $kelompok[$r->kelompok] ?? $r->kelompok }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</main>
@endsection

@push('scripts')
<script>
  (function () {
    const tabs = { fire: ['tab-fire', 'pane-fire'], health: ['tab-health', 'pane-health'] };
    Object.keys(tabs).forEach(function (key) {
      document.getElementById(tabs[key][0]).addEventListener('click', function () {
        Object.keys(tabs).forEach(function (k) {
          document.getElementById(tabs[k][0]).setAttribute('aria-selected', k === key);
          document.getElementById(tabs[k][1]).hidden = k !== key;
        });
      });
    });
  })();
</script>
@endpush
