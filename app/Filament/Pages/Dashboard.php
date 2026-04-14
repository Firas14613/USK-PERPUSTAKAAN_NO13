<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BukuResource;
use App\Filament\Resources\PeminjamanResource;
use App\Filament\Resources\SiswaResource;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Siswa;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;

class Dashboard extends BaseDashboard implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.dashboard';

    public string $search = '';

    public function getHeading(): string | Htmlable
    {
        return '';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Peminjaman::query()
                    ->with(['siswa.user', 'buku'])
                    ->where('status', 'pending')
                    ->latest('tanggal_pinjam'),
            )
            ->columns([
                TextColumn::make('siswa.user.nama_lengkap')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('buku.judul')
                    ->label('Book Title')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('tanggal_pinjam')
                    ->label('Request Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'dipinjam',
                        'success' => 'dikembalikan',
                        'danger' => ['terlambat', 'hilang'],
                        'info' => 'ditolak',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Peminjaman $record) {
                        DB::transaction(function () use ($record) {
                            $record->refresh();
                            $buku = Buku::query()->lockForUpdate()->findOrFail($record->buku_id);

                            if ($buku->stok < 1) {
                                Notification::make()
                                    ->title('Stok buku habis, tidak bisa approve.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $buku->decrement('stok');

                            $batasHari = (int) config('perpustakaan.batas_hari_pinjam', 7);
                            $tanggalPinjam = now();

                            $record->update([
                                'admin_id' => auth()->id(),
                                'status' => 'dipinjam',
                                'tanggal_pinjam' => $tanggalPinjam,
                                'batas_pengembalian' => Carbon::parse($tanggalPinjam)->addDays($batasHari)->toDateString(),
                            ]);
                        });

                        Notification::make()
                            ->title('Request disetujui.')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Peminjaman $record) {
                        $record->update([
                            'admin_id' => auth()->id(),
                            'status' => 'ditolak',
                            'catatan' => 'Ditolak via dashboard.',
                        ]);

                        Notification::make()
                            ->title('Request ditolak.')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 25]);
    }

    protected function getViewData(): array
    {
        $query = trim($this->search);
        $hasQuery = mb_strlen($query) >= 2;
        $like = '%' . str_replace('%', '\\%', $query) . '%';

        $bukuResults = $hasQuery
            ? Buku::query()
                ->select(['id', 'kode_buku', 'judul'])
                ->where('judul', 'like', $like)
                ->orWhere('kode_buku', 'like', $like)
                ->limit(5)
                ->get()
            : collect();

        $siswaResults = $hasQuery
            ? Siswa::query()
                ->select(['id', 'nis', 'user_id'])
                ->with(['user:id,nama_lengkap'])
                ->where(function ($subQuery) use ($like) {
                    $subQuery
                        ->where('nis', 'like', $like)
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('nama_lengkap', 'like', $like));
                })
                ->limit(5)
                ->get()
            : collect();

        $peminjamanResults = $hasQuery
            ? Peminjaman::query()
                ->select(['id', 'kode_peminjaman', 'status', 'siswa_id', 'buku_id'])
                ->with(['siswa.user:id,nama_lengkap', 'buku:id,judul'])
                ->where('kode_peminjaman', 'like', $like)
                ->limit(5)
                ->get()
            : collect();

        return [
            'counts' => [
                'total_buku' => Buku::query()->count(),
                'total_siswa_aktif' => Siswa::query()->where('status', 'aktif')->count(),
                'pending' => Peminjaman::query()->where('status', 'pending')->count(),
                'terlambat' => Peminjaman::query()->where('status', 'terlambat')->count(),
            ],
            'alerts' => [
                'new_books_7d' => Buku::query()->where('created_at', '>=', now()->subDays(7))->count(),
                'pending_requests' => Peminjaman::query()->where('status', 'pending')->count(),
                'overdue' => Peminjaman::query()->where('status', 'terlambat')->count(),
            ],
            'searchResults' => [
                'query' => $query,
                'hasQuery' => $hasQuery,
                'buku' => $bukuResults->map(fn (Buku $buku) => [
                    'title' => $buku->judul,
                    'subtitle' => $buku->kode_buku,
                    'url' => BukuResource::getUrl('edit', ['record' => $buku]),
                ])->all(),
                'siswa' => $siswaResults->map(fn (Siswa $siswa) => [
                    'title' => $siswa->user?->nama_lengkap ?? $siswa->nis,
                    'subtitle' => $siswa->nis,
                    'url' => SiswaResource::getUrl('edit', ['record' => $siswa]),
                ])->all(),
                'peminjaman' => $peminjamanResults->map(fn (Peminjaman $peminjaman) => [
                    'title' => $peminjaman->kode_peminjaman,
                    'subtitle' => ($peminjaman->siswa?->user?->nama_lengkap ?? '-') . ' • ' . ($peminjaman->buku?->judul ?? '-') . ' • ' . $peminjaman->status,
                    'url' => PeminjamanResource::getUrl('index') . '?tableSearch=' . urlencode($peminjaman->kode_peminjaman),
                ])->all(),
            ],
        ];
    }
}
