<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FireReport;
use App\Models\HealthReport;

class DashboardController extends Controller
{
    public function index()
    {
        $labels = collect(config('nafasdarurat.symptoms'))->pluck('label', 'id');

        $fire = FireReport::query()
            ->orderByRaw("case state when 'pending' then 0 when 'approved' then 1 else 2 end")
            ->latest()
            ->get();

        $summary = HealthReport::summary();
        $wilayah = collect($summary['wilayah'])->map(function ($v, $name) use ($labels) {
            arsort($v['sym']);
            $top = collect($v['sym'])->take(2)
                ->map(fn ($n, $id) => ($labels[$id] ?? $id).' ('.$n.')')
                ->implode(', ');

            return ['name' => $name, 'n' => $v['n'], 'top' => $top];
        })->values();

        $stats = [
            ['Gejala dilaporkan (24 jam)', HealthReport::where('created_at', '>', now()->subDay())->count()],
            ['Titik api menunggu verifikasi', $fire->where('state', 'pending')->count()],
            ['Titik api masih terbakar', $fire->where('state', 'approved')->where('status', 'api')->count()],
            ['Laporan ditolak', $fire->where('state', 'rejected')->count()],
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'fire' => $fire,
            'wilayah' => $wilayah,
            'recent' => HealthReport::latest()->limit(10)->get(),
            'labels' => $labels,
            'tab' => request('tab') === 'health' ? 'health' : 'fire',
        ]);
    }
}
