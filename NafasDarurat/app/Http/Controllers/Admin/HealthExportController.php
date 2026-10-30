<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthReport;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HealthExportController extends Controller
{
    public function csv(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
            fputcsv($out, ['id', 'waktu', 'nama', 'kota', 'kelurahan', 'gejala', 'lama', 'kelompok']);

            foreach (HealthReport::orderBy('id')->cursor() as $r) {
                fputcsv($out, array_map([$this, 'safe'], [
                    $r->id,
                    $r->created_at->toIso8601String(),
                    $r->nama,
                    $r->kota,
                    $r->kelurahan,
                    implode('|', $r->gejala),
                    $r->durasi,
                    $r->kelompok,
                ]));
            }

            fclose($out);
        }, 'laporan-gejala.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Sel yang diawali = + - @ diberi tanda ' supaya tidak dieksekusi sebagai rumus di spreadsheet. */
    private function safe(mixed $v): string
    {
        $s = (string) $v;

        return preg_match('/^[=+\-@\t\r]/', $s) ? "'".$s : $s;
    }
}
