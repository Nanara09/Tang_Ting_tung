<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Laporan gejala anonim: tanpa nama, kontak, atau lokasi tepat.
        Schema::create('health_reports', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kota', 40);
            $table->string('kelurahan', 40);
            $table->json('gejala');            // contoh: ["batuk","mata"]
            $table->string('durasi', 10);      // baru | sedang | lama
            $table->string('kelompok', 10);    // umum | balita | hamil | asma | lansia
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_reports');
    }
};
