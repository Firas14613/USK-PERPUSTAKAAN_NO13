<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanResource\Pages;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class PeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;

    public static function getModelLabel(): string
    {
        return 'Peminjaman';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Peminjaman';
    }

    protected static ?string $slug = 'peminjaman';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Perpustakaan';

    protected static ?string $navigationLabel = 'Peminjaman';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('kode_peminjaman')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('siswa.user.nama_lengkap')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('buku.judul')
                    ->label('Buku')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('tanggal_pinjam')
                    ->label('Tgl Pinjam')
                    ->date()
                    ->sortable(),
                TextColumn::make('batas_pengembalian')
                    ->label('Batas Kembali')
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
                    ])
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'dipinjam' => 'Dipinjam',
                        'dikembalikan' => 'Dikembalikan',
                        'terlambat' => 'Terlambat',
                        'ditolak' => 'Ditolak',
                        'hilang' => 'Hilang',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Peminjaman $record) => $record->status === 'pending')
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
                    }),

                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (Peminjaman $record) => $record->status === 'pending')
                    ->form([
                        Textarea::make('catatan')
                            ->label('Alasan / Catatan')
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function (Peminjaman $record, array $data) {
                        $record->update([
                            'admin_id' => auth()->id(),
                            'status' => 'ditolak',
                            'catatan' => $data['catatan'],
                        ]);
                    }),

                Action::make('kembalikan')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('info')
                    ->visible(fn (Peminjaman $record) => in_array($record->status, ['dipinjam', 'terlambat'], true))
                    ->form(fn (Peminjaman $record) => [
                        DatePicker::make('tanggal_kembali_aktual')
                            ->label('Tanggal Kembali Aktual')
                            ->default(now())
                            ->required()
                            ->minDate($record->tanggal_pinjam ? Carbon::parse($record->tanggal_pinjam)->toDateString() : null)
                            ->maxDate(now())
                            ->rule($record->tanggal_pinjam ? ('after_or_equal:' . Carbon::parse($record->tanggal_pinjam)->toDateString()) : null)
                            ->rule('before_or_equal:today')
                            ->reactive(),
                        Placeholder::make('keterlambatan')
                            ->label('Keterlambatan (hari)')
                            ->content(function (Get $get) use ($record) {
                                return self::hitungKeterlambatan($record, $get('tanggal_kembali_aktual'));
                            }),
                        Placeholder::make('denda_per_hari')
                            ->label('Denda Per Hari')
                            ->content(fn () => (int) config('perpustakaan.denda_per_hari', 1000)),
                        Placeholder::make('denda_otomatis')
                            ->label('Denda Otomatis')
                            ->content(function (Get $get) use ($record) {
                                return self::hitungDendaOtomatis($record, $get('tanggal_kembali_aktual'));
                            }),
                        TextInput::make('denda_dibayar')
                            ->label('Denda Dibayar (Override)')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Kosongkan untuk memakai denda otomatis.'),
                        Select::make('kondisi_buku')
                            ->label('Kondisi Buku')
                            ->options([
                                'baik' => 'Baik',
                                'rusak' => 'Rusak',
                                'hilang' => 'Hilang',
                            ])
                            ->default('baik')
                            ->required(),
                        Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(3)
                            ->required(function (Get $get) use ($record) {
                                $otomatis = self::hitungDendaOtomatis($record, $get('tanggal_kembali_aktual'));
                                $dibayar = $get('denda_dibayar');

                                return $dibayar !== null && (float) $dibayar !== (float) $otomatis;
                            }),
                    ])
                    ->action(function (Peminjaman $record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $record->refresh();

                            $tanggalKembali = Carbon::parse($data['tanggal_kembali_aktual'])->toDateString();
                            $keterlambatan = self::hitungKeterlambatan($record, $tanggalKembali);
                            $dendaPerHari = (int) config('perpustakaan.denda_per_hari', 1000);
                            $dendaOtomatis = $keterlambatan * $dendaPerHari;

                            $dendaDibayar = $data['denda_dibayar'] ?? null;
                            if ($dendaDibayar === null || $dendaDibayar === '') {
                                $dendaDibayar = $dendaOtomatis;
                            }

                            Pengembalian::create([
                                'peminjaman_id' => $record->id,
                                'admin_id' => auth()->id(),
                                'tanggal_kembali_aktual' => $tanggalKembali,
                                'keterlambatan' => $keterlambatan,
                                'denda_per_hari' => $dendaPerHari,
                                'denda_otomatis' => $dendaOtomatis,
                                'denda_dibayar' => $dendaDibayar,
                                'kondisi_buku' => $data['kondisi_buku'],
                                'catatan' => $data['catatan'] ?? null,
                            ]);

                            if ($data['kondisi_buku'] === 'hilang') {
                                $statusAkhir = 'hilang';
                            } else {
                                $statusAkhir = 'dikembalikan';
                            }

                            $record->update([
                                'admin_id' => auth()->id(),
                                'tanggal_kembali' => $tanggalKembali,
                                'status' => $statusAkhir,
                                'denda' => $dendaDibayar,
                                'catatan' => $data['catatan'] ?? $record->catatan,
                            ]);

                            if ($data['kondisi_buku'] !== 'hilang') {
                                Buku::query()->whereKey($record->buku_id)->increment('stok');
                            }
                        });
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeminjamen::route('/'),
        ];
    }

    private static function hitungKeterlambatan(Peminjaman $record, $tanggalKembali): int
    {
        if (empty($record->batas_pengembalian) || empty($tanggalKembali)) {
            return 0;
        }

        $batas = Carbon::parse($record->batas_pengembalian)->startOfDay();
        $aktual = Carbon::parse($tanggalKembali)->startOfDay();

        if ($aktual->lte($batas)) {
            return 0;
        }

        return $batas->diffInDays($aktual);
    }

    private static function hitungDendaOtomatis(Peminjaman $record, $tanggalKembali): int
    {
        $keterlambatan = self::hitungKeterlambatan($record, $tanggalKembali);
        $dendaPerHari = (int) config('perpustakaan.denda_per_hari', 1000);

        return $keterlambatan * $dendaPerHari;
    }
}
