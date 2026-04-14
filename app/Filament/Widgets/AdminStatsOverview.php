<?php

namespace App\Filament\Widgets;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Siswa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Buku', Buku::query()->count()),
            Stat::make('Siswa Aktif', Siswa::query()->where('status', 'aktif')->count()),
            Stat::make('Total Peminjaman', Peminjaman::query()->count()),
            Stat::make('Pending', Peminjaman::query()->where('status', 'pending')->count())
                ->color('gray'),
            Stat::make('Terlambat', Peminjaman::query()->where('status', 'terlambat')->count())
                ->color('danger'),
        ];
    }
}
