<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Ekspor PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->url(fn () => route('admin.laporan.exportPdf', $this->getExportQueryParams()))
                ->openUrlInNewTab(),
        ];
    }

    protected function getExportQueryParams(): array
    {
        return [
            'tableSearch' => $this->tableSearch,
            'tableFilters' => $this->tableFilters,
            'tableSortColumn' => $this->tableSortColumn,
            'tableSortDirection' => $this->tableSortDirection,
        ];
    }

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
                Filter::make('rentang_tanggal')
                    ->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari'),
                        DatePicker::make('to')
                            ->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $query, $date) => $query->whereDate('tanggal_pinjam', '>=', $date))
                            ->when($data['to'] ?? null, fn (Builder $query, $date) => $query->whereDate('tanggal_pinjam', '<=', $date));
                    }),
            ])
            ->actions([]);
    }

    protected function getViewData(): array
    {
        $totalPinjaman = Peminjaman::query()->count();
        $totalTerlambat = Peminjaman::query()->where('status', 'terlambat')->count();
        $totalDenda = (float) (Peminjaman::query()->sum('denda') ?? 0);
        $activeDenda = Peminjaman::query()
            ->whereNotNull('denda')
            ->where('denda', '>', 0)
            ->count();

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
        $this->tableSearch = '';
        $this->tableFilters = [];
        $this->tableSortColumn = null;
        $this->tableSortDirection = null;
        $this->resetTable();
    }
}
