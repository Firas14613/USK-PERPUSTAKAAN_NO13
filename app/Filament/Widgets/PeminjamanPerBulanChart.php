<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PeminjamanPerBulanChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Peminjaman (12 Bulan Terakhir)';

    protected function getData(): array
    {
        $start = now()->startOfMonth()->subMonths(11);
        $end = now()->endOfMonth();

        $rows = Peminjaman::query()
            ->selectRaw("DATE_FORMAT(tanggal_pinjam, '%Y-%m') as ym, COUNT(*) as total")
            ->whereBetween('tanggal_pinjam', [$start->toDateString(), $end->toDateString()])
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        $labels = [];
        $data = [];

        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $ym = $cursor->format('Y-m');
            $labels[] = $cursor->format('M Y');
            $data[] = (int) ($rows[$ym] ?? 0);
            $cursor->addMonth();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Peminjaman',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

