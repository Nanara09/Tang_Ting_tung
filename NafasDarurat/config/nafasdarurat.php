<?php

/*
 * Konstanta Nafas Darurat (dulu ada di assets/js/data.js versi demo).
 * Semua kecuali 'photo' juga dikirim ke JavaScript lewat window.NA_CONFIG.
 */
return [
    'cities' => [
        ['id' => 'plk', 'name' => 'Palangka Raya', 'lat' => -2.2096, 'lng' => 113.9213, 'sample' => 312],
        ['id' => 'ptk', 'name' => 'Pontianak', 'lat' => -0.0263, 'lng' => 109.3425, 'sample' => 178],
        ['id' => 'jmb', 'name' => 'Jambi', 'lat' => -1.6101, 'lng' => 103.6131, 'sample' => 235],
        ['id' => 'plg', 'name' => 'Palembang', 'lat' => -2.9761, 'lng' => 104.7754, 'sample' => 96],
    ],

    'groups' => [
        ['id' => 'umum', 'name' => 'Umum', 'tip' => 'Minum air putih yang cukup dan perbanyak istirahat.'],
        ['id' => 'balita', 'name' => 'Balita', 'tip' => 'Jaga balita tetap di dalam ruangan dan jangan biarkan bermain di luar.'],
        ['id' => 'hamil', 'name' => 'Ibu hamil', 'tip' => 'Batasi keluar rumah dan segera konsultasi bila sesak atau pusing.'],
        ['id' => 'asma', 'name' => 'Penderita asma', 'tip' => 'Siapkan obat pelega sesuai anjuran dokter dan pantau gejala.'],
        ['id' => 'lansia', 'name' => 'Lansia', 'tip' => 'Lansia dengan sakit jantung atau paru perlu lebih banyak istirahat di dalam ruangan.'],
    ],

    'levels' => [
        'baik' => 'Baik',
        'sedang' => 'Sedang',
        'tidak' => 'Tidak sehat',
        'sangat' => 'Sangat tidak sehat',
        'bahaya' => 'Berbahaya',
    ],

    'advice' => [
        'baik' => ['Aktivitas di luar ruangan aman dilakukan.', 'Tetap pantau kondisi udara secara berkala.', 'Tidak perlu masker khusus.'],
        'sedang' => ['Aktivitas di luar ruangan masih wajar.', 'Kelompok sensitif sebaiknya mengurangi aktivitas berat di luar.', 'Siapkan masker bila bau asap mulai tercium.'],
        'tidak' => ['Kurangi aktivitas berat di luar ruangan.', 'Pakai masker N95/KN95 saat harus keluar.', 'Tutup jendela dan pintu bila asap masuk.'],
        'sangat' => ['Hindari aktivitas di luar ruangan.', 'Pakai masker N95/KN95 bila terpaksa keluar.', 'Tetap di dalam ruangan dengan jendela tertutup.'],
        'bahaya' => ['Tetap di dalam ruangan dan tunda aktivitas di luar.', 'Pakai masker N95/KN95 bila terpaksa keluar.', 'Segera ke fasilitas kesehatan bila sesak napas.'],
    ],

    'symptoms' => [
        ['id' => 'batuk', 'label' => 'Batuk'],
        ['id' => 'sesak', 'label' => 'Sesak napas'],
        ['id' => 'mata', 'label' => 'Mata perih'],
        ['id' => 'tenggorokan', 'label' => 'Sakit tenggorokan'],
        ['id' => 'kepala', 'label' => 'Sakit kepala'],
        ['id' => 'pusing', 'label' => 'Pusing'],
        ['id' => 'nyeri', 'label' => 'Nyeri dada'],
        ['id' => 'hidung', 'label' => 'Hidung iritasi'],
    ],

    'durations' => [
        ['id' => 'baru', 'name' => 'Hari ini'],
        ['id' => 'sedang', 'name' => '2-3 hari'],
        ['id' => 'lama', 'name' => 'Lebih dari 3 hari'],
    ],

    'status' => [
        'api' => 'Masih terbakar',
        'tangani' => 'Dalam penanganan',
        'padam' => 'Sudah padam',
    ],

    'fac_types' => [
        'oksigen' => 'Rumah oksigen',
        'puskesmas' => 'Puskesmas',
        'posko' => 'Posko',
    ],

    // Pengaturan foto laporan titik api (diproses ulang di server, EXIF otomatis hilang).
    'photo' => ['max_dimension' => 800, 'quality' => 72],
];
