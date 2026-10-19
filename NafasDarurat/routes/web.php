<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FireReportController as AdminFireReportController;
use App\Http\Controllers\Admin\HealthExportController;
use App\Http\Controllers\AirQualityController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\FireReportController;
use App\Http\Controllers\HealthReportController;
use App\Http\Controllers\PageController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

/* ---------- Halaman publik ---------- */
Route::controller(PageController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/cek-kesehatan', 'cek')->name('cek');
    Route::get('/lapor-gejala', 'laporGejala')->name('lapor.gejala');
    Route::get('/lapor-titik-api', 'laporApi')->name('lapor.api');
});

/* ---------- Endpoint JSON untuk JavaScript (session + CSRF aktif) ---------- */
Route::prefix('api')->group(function () {
    Route::get('udara', [AirQualityController::class, 'index']);
    Route::get('fasilitas', [FacilityController::class, 'index']);

    Route::get('gejala/ringkasan', [HealthReportController::class, 'summary']);
    Route::post('gejala', [HealthReportController::class, 'store'])->middleware('throttle:10,1');

    Route::get('titik-api', [FireReportController::class, 'index']);
    Route::post('titik-api', [FireReportController::class, 'store'])->middleware('throttle:6,1');
    Route::post('titik-api/{fireReport}/vote', [FireReportController::class, 'vote'])->middleware('throttle:30,1');
});

/* ---------- Admin ---------- */
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('masuk', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');

    Route::middleware(EnsureAdmin::class)->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('keluar', [AuthController::class, 'logout'])->name('logout');
        Route::patch('titik-api/{fireReport}', [AdminFireReportController::class, 'update'])->name('fire.update');
        Route::delete('titik-api/{fireReport}', [AdminFireReportController::class, 'destroy'])->name('fire.destroy');
        Route::get('gejala/csv', [HealthExportController::class, 'csv'])->name('health.csv');
    });
});
