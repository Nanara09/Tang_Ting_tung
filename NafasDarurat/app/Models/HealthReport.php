<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthReport extends Model
{
    protected $fillable = ['kota', 'kelurahan', 'gejala', 'durasi', 'kelompok'];

    protected $casts = ['gejala' => 'array'];

    /**
     * Ringkasan per wilayah dan per gejala.
     * Untuk data sangat besar, pindahkan ke tabel pivot atau query agregat.
     *
     * @return array{wilayah: array<string, array{n: int, sym: array<string, int>}>, gejala: array<string, int>}
     */
    public static function summary(): array
    {
        $wilayah = [];
        $gejala = [];

        foreach (static::query()->select(['kelurahan', 'kota', 'gejala'])->cursor() as $r) {
            $name = $r->kelurahan.', '.$r->kota;
            $wilayah[$name]['n'] = ($wilayah[$name]['n'] ?? 0) + 1;

            foreach ($r->gejala as $s) {
                $wilayah[$name]['sym'][$s] = ($wilayah[$name]['sym'][$s] ?? 0) + 1;
                $gejala[$s] = ($gejala[$s] ?? 0) + 1;
            }
        }

        uasort($wilayah, fn ($a, $b) => $b['n'] <=> $a['n']);
        arsort($gejala);

        return compact('wilayah', 'gejala');
    }
}
