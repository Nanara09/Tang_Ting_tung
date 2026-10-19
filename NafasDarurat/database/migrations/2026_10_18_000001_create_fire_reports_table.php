<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fire_reports', function (Blueprint $table) {
            $table->id();
            $table->string('place', 80);
            $table->string('description', 300)->nullable();
            $table->string('status', 10);                    // api | tangani | padam (kondisi api)
            $table->decimal('lat', 9, 6);
            $table->decimal('lng', 10, 6);
            $table->string('photo_path')->nullable();
            $table->unsignedInteger('yes_count')->default(0); // konfirmasi "masih ada api"
            $table->unsignedInteger('no_count')->default(0);  // konfirmasi "sudah padam"
            $table->string('state', 10)->default('pending')->index(); // pending | approved | rejected
            $table->timestamps();
        });

        // Satu orang (session) hanya boleh memberi satu suara per jenis per laporan.
        Schema::create('fire_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fire_report_id')->constrained()->cascadeOnDelete();
            $table->string('voter_key', 64);
            $table->string('choice', 1); // y | n
            $table->timestamps();
            $table->unique(['fire_report_id', 'voter_key', 'choice']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fire_votes');
        Schema::dropIfExists('fire_reports');
    }
};
