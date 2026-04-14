<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengembalianResource\Pages;
use App\Models\Pengembalian;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PengembalianResource extends Resource
{
    protected static ?string $model = Pengembalian::class;

    protected static ?string $slug = 'pengembalian';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    protected static ?string $navigationGroup = 'Perpustakaan';

    protected static ?string $navigationLabel = 'Pengembalian';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('peminjaman.kode_peminjaman')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('peminjaman.siswa.user.nama_lengkap')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('peminjaman.buku.judul')
                    ->label('Buku')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('tanggal_kembali_aktual')
                    ->label('Tgl Kembali')
                    ->date()
                    ->sortable(),
                TextColumn::make('peminjaman.status')
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
                TextColumn::make('keterlambatan')
                    ->label('Terlambat (hari)')
                    ->sortable(),
                TextColumn::make('denda_dibayar')
                    ->label('Denda Dibayar')
                    ->formatStateUsing(fn ($state) => $state === null ? '—' : ('Rp '.number_format((float) $state, 0, ',', '.')))
                    ->sortable(),
                TextColumn::make('kondisi_buku')
                    ->label('Kondisi')
                    ->badge()
                    ->colors([
                        'success' => 'baik',
                        'warning' => 'rusak',
                        'danger' => 'hilang',
                    ])
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('kondisi_buku')
                    ->label('Kondisi')
                    ->options([
                        'baik' => 'Baik',
                        'rusak' => 'Rusak',
                        'hilang' => 'Hilang',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPengembalians::route('/'),
        ];
    }
}
