<?php

return [
    'denda_per_hari' => (int) env('DENDA_PER_HARI', 1000),
    'batas_hari_pinjam' => (int) env('BATAS_HARI_PINJAM', 7),
    'max_pinjam_siswa' => (int) env('MAX_PINJAM_SISWA', 3),
    'storage_cover' => env('STORAGE_COVER', 'public/covers'),
];

