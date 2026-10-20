<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFireReportRequest;
use App\Models\FireReport;
use App\Models\FireVote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FireReportController extends Controller
{
    /** Hanya laporan yang sudah disetujui admin yang tampil di peta publik. */
    public function index(Request $request): JsonResponse
    {
        $reports = FireReport::approved()->latest()->get();

        $voted = FireVote::where('voter_key', $this->voterKey($request))
            ->whereIn('fire_report_id', $reports->pluck('id'))
            ->get()
            ->groupBy('fire_report_id');
return response()->json(
    $reports->map(function ($r) use ($voted) {
        $data = (array) $r;

        $data['votes'] = ($voted[$r->id] ?? collect())
            ->pluck('choice')
            ->all();

        return $data;
    })->values()
);
    }

    public function store(StoreFireReportRequest $request): JsonResponse
    {
        $data = $request->validated();

        FireReport::create([
            'place' => $data['place'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'photo_path' => $request->hasFile('photo') ? $this->storePhoto($request->file('photo')) : null,
        ]); // state otomatis "pending" sampai disetujui admin

        return response()->json(['message' => 'Laporan terkirim dan menunggu verifikasi admin.'], 201);
    }

    /** Konfirmasi warga: choice "y" (masih ada api) atau "n" (sudah padam). Kirim lagi untuk membatalkan. */
    public function vote(Request $request, FireReport $fireReport): JsonResponse
    {
        abort_unless($fireReport->state === 'approved', 404);

        $choice = $request->validate(['choice' => ['required', 'in:y,n']])['choice'];
        $key = $this->voterKey($request);
        $column = $choice === 'y' ? 'yes_count' : 'no_count';

        DB::transaction(function () use ($fireReport, $choice, $key, $column) {
            $existing = FireVote::where([
                'fire_report_id' => $fireReport->id,
                'voter_key' => $key,
                'choice' => $choice,
            ])->first();

            if ($existing) {
                $existing->delete();
                if ($fireReport->{$column} > 0) {
                    $fireReport->decrement($column);
                }
            } else {
                FireVote::create(['fire_report_id' => $fireReport->id, 'voter_key' => $key, 'choice' => $choice]);
                $fireReport->increment($column);
            }
        });

        $fireReport->refresh();
        $mine = FireVote::where(['fire_report_id' => $fireReport->id, 'voter_key' => $key])->pluck('choice')->all();

        return response()->json($fireReport->toPublic($mine));
    }

    /** Penanda pemilih berbasis session (di-hash). Bukan pengamanan kuat, hanya pencegah klik berulang. */
    private function voterKey(Request $request): string
    {
        return hash('sha256', $request->session()->getId());
    }

    /**
     * Baca ulang gambar dengan GD lalu simpan sebagai JPEG yang diperkecil.
     * Karena di-encode ulang, EXIF (termasuk koordinat GPS) otomatis hilang,
     * dan file yang bukan gambar asli ditolak.
     */
    private function storePhoto(UploadedFile $file): string
    {
        $path = $file->getRealPath();
        $src = $file->getMimeType() === 'image/png' ? @imagecreatefrompng($path) : @imagecreatefromjpeg($path);

        if (! $src) {
            throw ValidationException::withMessages(['photo' => 'Gambar tidak bisa dibaca.']);
        }

        $max = (int) config('nafasdarurat.photo.max_dimension', 800);
        $w = imagesx($src);
        $h = imagesy($src);
        $scale = min(1, $max / max($w, $h));
        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));

        $dst = imagecreatetruecolor($nw, $nh);
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255)); // latar putih untuk PNG transparan
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        ob_start();
        imagejpeg($dst, null, (int) config('nafasdarurat.photo.quality', 72));
        $jpeg = ob_get_clean();

        $name = 'fire-photos/'.Str::uuid().'.jpg';
        Storage::disk('public')->put($name, $jpeg);

        return $name;
    }
}
