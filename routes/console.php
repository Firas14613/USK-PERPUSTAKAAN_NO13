<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('peminjaman:update-terlambat')
    ->everyTenMinutes()
    ->description('Auto: ubah status dipinjam yang lewat batas menjadi terlambat');
