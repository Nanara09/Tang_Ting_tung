<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        // Masih contoh (alamat dan jam buka belum diisi). Ganti dengan data asli.
        $rows = [
            ['oksigen', 'Rumah Oksigen Kelurahan A', 'Palangka Raya'],
            ['puskesmas', 'Puskesmas B', 'Palangka Raya'],
            ['posko', 'Posko Karhutla Kota C', 'Pontianak'],
            ['oksigen', 'Rumah Oksigen Kecamatan D', 'Jambi'],
            ['puskesmas', 'Puskesmas E', 'Palembang'],
            ['posko', 'Posko Kesehatan F', 'Jambi'],
        ];

        foreach ($rows as [$type, $name, $city]) {
            Facility::updateOrCreate(
                ['name' => $name, 'city' => $city],
                ['type' => $type, 'address' => 'Jl. [alamat lengkap]', 'hours' => '[diisi admin]']
            );
        }
    }
}
