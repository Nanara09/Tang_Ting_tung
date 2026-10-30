<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHealthReportRequest;
use App\Models\HealthReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HealthReportController extends Controller
{
    /** Data untuk grafik beranda: 5 wilayah teratas dan 3 gejala terbanyak. */
    public function summary(): JsonResponse
    {
        $data = Cache::remember('health.summary', 30, function () {
            $s = HealthReport::summary();

            return [
                'wilayah' => collect($s['wilayah'])->map(fn ($v, $name) => ['name' => $name, 'n' => $v['n']])->values()->take(5)->all(),
                'gejala' => collect($s['gejala'])->map(fn ($n, $id) => ['id' => $id, 'n' => $n])->values()->take(3)->all(),
            ];
        });

        return response()->json($data);
    }

    public function store(StoreHealthReportRequest $request): JsonResponse
    {
        $data = $request->validated();

        HealthReport::create([
            'nama' => $data['nama'],
            'kota' => $data['kota'],
            'kelurahan' => $data['kelurahan'],
            'gejala' => array_values(array_unique($data['gejala'])),
            'durasi' => $data['durasi'],
            'kelompok' => $data['kelompok'],
        ]);

        Cache::forget('health.summary');

        return response()->json(['message' => 'Laporan tercatat.'], 201);
    }
}
