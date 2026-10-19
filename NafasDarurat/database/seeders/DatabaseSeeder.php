<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([FacilitySeeder::class, AdminSeeder::class]);

        // Data contoh (laporan palsu) hanya untuk lokal, supaya tidak masuk produksi.
        if (app()->environment('local')) {
            $this->call(SampleDataSeeder::class);
        }
    }
}
