<?php

namespace App\Http\Controllers;

use App\Support\Ispu;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AirQualityController extends Controller
{
    private const URL = 'https://air-quality-api.open-meteo.com/v1/air-quality';

    /**
     * PM2.5 tiap kota dari Open-Meteo (gratis, tanpa API key), di-cache 10 menit di server
     * supaya semua pengunjung berbagi satu hasil. Kalau gagal, pakai angka contoh.
     */
    public function index(): JsonResponse
    {
        $cities = collect(config('nafasdarurat.cities'));

        $missing = $cities->filter(
            fn ($c) => ! Cache::has("aq.{$c['id']}") && ! Cache::has("aq.fail.{$c['id']}")
        );

        if ($missing->isNotEmpty()) {
            try {
                $responses = Http::pool(fn (Pool $pool) => $missing->map(
                    fn ($c) => $pool->as($c['id'])->timeout(4)->get(self::URL, [
                        'latitude' => $c['lat'],
                        'longitude' => $c['lng'],
                        'current' => 'pm2_5',
                    ])
                )->values()->all());

                foreach ($missing as $c) {
                    $res = $responses[$c['id']] ?? null;
                    $pm = $res instanceof Response && $res->ok() ? $res->json('current.pm2_5') : null;

                    if (is_numeric($pm)) {
                        Cache::put("aq.{$c['id']}", (float) $pm, 600);
                    } else {
                        Cache::put("aq.fail.{$c['id']}", 1, 60); // jangan menghajar API saat sedang gagal
                    }
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $out = [];
        foreach ($cities as $c) {
            $pm = Cache::get("aq.{$c['id']}");
            $out[$c['id']] = $pm === null
                ? ['ispu' => $c['sample'], 'live' => false]
                : ['ispu' => Ispu::fromPm25($pm), 'live' => true];
        }

        return response()->json($out);
    }
}
