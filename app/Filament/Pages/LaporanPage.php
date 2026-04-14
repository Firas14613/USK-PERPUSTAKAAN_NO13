<?php

namespace App\Filament\Pages;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LaporanPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $slug = 'laporan';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Perpustakaan';

    protected static ?string $navigationLabel = 'Laporan';

    protected static string $view = 'filament.pages.laporan-page';

    public ?string $searchName = null;

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public ?string $filterStatus = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(Peminjaman::query()->with(['siswa.user', 'buku']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('siswa.user.nama_lengkap')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('buku.judul')
                    ->label('Judul Buku')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('tanggal_pinjam')
                    ->label('Tgl Pinjam')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('tanggal_kembali')
                    ->label('Tgl Kembali')
                    ->date('d M Y'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'dipinjam',
                        'success' => 'dikembalikan',
                        'danger' => ['terlambat', 'hilang'],
                        'info' => 'ditolak',
                    ])
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'Pending',
                        'dipinjam' => 'Dipinjam',
                        'dikembalikan' => 'Dikembalikan',
                        'terlambat' => 'Terlambat',
                        'ditolak' => 'Ditolak',
                        'hilang' => 'Hilang',
                        default => ucfirst($state),
                    }),
                TextColumn::make('denda')
                    ->label('Denda')
                    ->money('IDR')
                    ->formatStateUsing(fn ($state) => $state ? 'Rp '.number_format($state, 0, ',', '.') : '-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Peminjaman')
                    ->options([
                        'pending' => 'Pending',
                        'dipinjam' => 'Dipinjam',
                        'dikembalikan' => 'Dikembalikan',
                        'terlambat' => 'Terlambat',
                        'ditolak' => 'Ditolak',
                        'hilang' => 'Hilang',
                    ]),
            ])
            ->actions([]);
    }

    protected function getViewData(): array
    {
        $totalPinjaman = Peminjaman::query()->count();
        $totalTerlambat = Peminjaman::query()->where('status', 'terlambat')->count();
        $totalDenda = Pengembalian::query()->sum('denda_dibayar') ?? 0;
        $activeDenda = Pengembalian::query()->where('keterlambatan', '>', 0)->count();

        $bukuTerpopuler = Peminjaman::query()
            ->selectRaw('buku_id, COUNT(*) as total')
            ->groupBy('buku_id')
            ->orderByDesc('total')
            ->with('buku:id,judul')
            ->first();

        return [
            'totalPinjaman' => $totalPinjaman,
            'totalTerlambat' => $totalTerlambat,
            'totalDenda' => $totalDenda,
            'activeDenda' => $activeDenda,
            'bukuTerpopuler' => $bukuTerpopuler?->buku?->judul ?? '-',
        ];
    }

    public function applyFilters(): void
    {
        // Filters are handled by Filament tables automatically
        $this->resetTable();
    }

    public function resetFilters(): void
    {
        $this->searchName = null;
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->filterStatus = null;
        $this->resetTable();
    }
}
