<?php

namespace Database\Seeders;

use App\Models\FireReport;
use App\Models\HealthReport;
use Illuminate\Database\Seeder;

/** Data contoh yang sama dengan seed() di versi demo (bukan kejadian nyata). */
class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $fire = [
            ['Kel. A, Palangka Raya', 'Api terlihat di lahan kosong dekat permukiman.', 20, 'api', -2.23, 113.9, 12, 1, 'approved'],
            ['Kel. D, Pontianak', 'Asap tebal dari lahan gambut di tepi jalan.', 60, 'api', -0.05, 109.32, 7, 0, 'approved'],
            ['Kec. B, Jambi', 'Petugas sudah di lokasi, api mulai mengecil.', 120, 'tangani', -1.6, 103.64, 5, 2, 'approved'],
            ['Desa C, Ogan Ilir', 'Api sudah padam, masih ada sisa asap tipis.', 300, 'padam', -3.22, 104.64, 1, 9, 'approved'],
            ['Kel. F, Palembang', 'Api kecil di semak dekat jalan raya.', 10, 'api', -2.95, 104.76, 0, 0, 'pending'],
            ['Kec. H, Palangka Raya', 'Petugas pemadam sedang di lokasi.', 40, 'tangani', -2.19, 113.95, 0, 0, 'pending'],
        ];

        foreach ($fire as [$place, $desc, $minutesAgo, $status, $lat, $lng, $yes, $no, $state]) {
            FireReport::forceCreate([
                'place' => $place, 'description' => $desc, 'status' => $status,
                'lat' => $lat, 'lng' => $lng, 'yes_count' => $yes, 'no_count' => $no, 'state' => $state,
                'created_at' => now()->subMinutes($minutesAgo), 'updated_at' => now()->subMinutes($minutesAgo),
            ]);
        }

        // Laporan gejala dibuat deterministik agar grafik tidak berubah-ubah.
        $spec = [['Kel. A', 'Palangka Raya', 64], ['Kel. D', 'Pontianak', 48], ['Kec. B', 'Jambi', 31], ['Desa C', 'Ogan Ilir', 12], ['Kel. E', 'Palembang', 9]];
        $groups = ['umum', 'umum', 'balita', 'umum', 'asma', 'lansia', 'hamil'];
        $durs = ['baru', 'sedang', 'lama'];
        $rows = [];

        foreach ($spec as [$kel, $kota, $n]) {
            for ($i = 0; $i < $n; $i++) {
                $sym = [];
                if ($i % 5 !== 4) $sym[] = 'batuk';
                if ($i % 3 === 0) $sym[] = 'mata';
                if ($i % 7 === 0) $sym[] = 'sesak';
                if ($i % 4 === 0) $sym[] = 'tenggorokan';
                if (! $sym) $sym[] = 'pusing';

                $at = now()->subMinutes(($i * 97) % (7 * 24 * 60));
                $rows[] = [
                    'kota' => $kota, 'kelurahan' => $kel, 'gejala' => json_encode($sym),
                    'durasi' => $durs[$i % 3], 'kelompok' => $groups[$i % count($groups)],
                    'created_at' => $at->toDateTimeString(), 'updated_at' => $at->toDateTimeString(),
                ];
            }
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            HealthReport::insert($chunk);
        }
    }
}
