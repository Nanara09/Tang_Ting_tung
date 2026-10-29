
<?php

use App\Models\FireReport;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public API returns only approved fire reports', function () {
    $approved = new FireReport([
        'place' => 'Tes Palangka Raya',
        'description' => 'Laporan yang disetujui',
        'status' => 'api',
        'lat' => -2.21,
        'lng' => 113.92,
    ]);
    $approved->state = 'approved';
    $approved->save();

    $pending = new FireReport([
        'place' => 'Tes Belum Disetujui',
        'description' => 'Laporan menunggu verifikasi',
        'status' => 'api',
        'lat' => -2.22,
        'lng' => 113.93,
    ]);
    $pending->state = 'pending';
    $pending->save();

    $response = $this->getJson('/api/titik-api');

    $response->assertOk()
        ->assertJsonFragment([
            'place' => 'Tes Palangka Raya',
        ])
        ->assertJsonMissing([
            'place' => 'Tes Belum Disetujui',
        ]);
});




test('voting can be added and cancelled', function () {
    $report = new \App\Models\FireReport([
        'place' => 'Tes Voting',
        'description' => 'Laporan untuk pengujian',
        'status' => 'api',
        'lat' => -2.21,
        'lng' => 113.92,
    ]);

    $report->state = 'approved';
    $report->save();

    $payload = ['choice' => 'y'];

  $this->call(
    'POST',
    "/api/titik-api/{$report->id}/vote",
    $payload,
    ['voter_id' => 'test-voter-id'],
    [],
    ['HTTP_ACCEPT' => 'application/json']
)->assertOk()
  ->assertJsonPath('yes', 1);

$this->call(
    'POST',
    "/api/titik-api/{$report->id}/vote",
    $payload,
    ['voter_id' => 'test-voter-id'],
    [],
    ['HTTP_ACCEPT' => 'application/json']
)->assertOk()
  ->assertJsonPath('yes', 0);
  
    $this->assertDatabaseCount('fire_votes', 0);

    $report->refresh();
    expect($report->yes_count)->toBe(0);
});
