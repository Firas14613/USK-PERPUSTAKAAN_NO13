<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengembalianResource\Pages;
use App\Models\Pengembalian;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PengembalianResource extends Resource
{
    protected static ?string $model = Pengembalian::class;

    public static function getModelLabel(): string
    {
        return 'Pengembalian';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengembalian';
    }

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
            ->defaultSort('tanggal_kembali_aktual', 'desc')
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Buku & Siswa')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('peminjaman.buku.cover_image')
                            ->label('Cover')
                            ->height(180)
                            ->state(function (Pengembalian $record): ?string {
                                $path = $record->peminjaman?->buku?->cover_image;

                                if (blank($path)) {
                                    return null;
                                }

                                $path = ltrim(str_replace('public/', '', (string) $path), '/');

                                return asset('storage/' . $path);
                            }),
                        Section::make()
                            ->columns(1)
                            ->schema([
                                TextEntry::make('peminjaman.buku.judul')
                                    ->label('Judul Buku')
                                    ->weight('bold'),
                                TextEntry::make('peminjaman.buku.penulis')
                                    ->label('Penulis')
                                    ->color('gray'),
                                TextEntry::make('peminjaman.buku.kategori.nama_kategori')
                                    ->label('Kategori')
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('—'),
                                TextEntry::make('peminjaman.siswa.user.nama_lengkap')
                                    ->label('Nama Siswa')
                                    ->placeholder('—'),
                                TextEntry::make('peminjaman.siswa.nis')
                                    ->label('NIS')
                                    ->placeholder('—'),
                                TextEntry::make('peminjaman.siswa.kelas')
                                    ->label('Kelas')
                                    ->placeholder('—'),
                                TextEntry::make('peminjaman.kode_peminjaman')
                                    ->label('Kode Peminjaman')
                                    ->copyable(),
                                TextEntry::make('peminjaman.status')
                                    ->label('Status Peminjaman')
                                    ->badge()
                                    ->colors([
                                        'gray' => 'pending',
                                        'warning' => 'dipinjam',
                                        'success' => 'dikembalikan',
                                        'danger' => ['terlambat', 'hilang'],
                                        'info' => 'ditolak',
                                    ]),
                            ]),
                    ]),

                Section::make('Tanggal')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('peminjaman.tanggal_pinjam')
                            ->label('Tgl Pinjam')
                            ->date()
                            ->placeholder('—'),
                        TextEntry::make('peminjaman.batas_pengembalian')
                            ->label('Batas Kembali')
                            ->date()
                            ->placeholder('—'),
                        TextEntry::make('tanggal_kembali_aktual')
                            ->label('Tgl Kembali Aktual')
                            ->date()
                            ->placeholder('—'),
                    ]),

                Section::make('Denda')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('keterlambatan')
                            ->label('Terlambat (hari)')
                            ->placeholder('0'),
                        TextEntry::make('denda_per_hari')
                            ->label('Denda / Hari')
                            ->formatStateUsing(fn ($state) => $state === null ? '—' : ('Rp ' . number_format((float) $state, 0, ',', '.'))),
                        TextEntry::make('denda_otomatis')
                            ->label('Denda Otomatis')
                            ->formatStateUsing(fn ($state) => $state === null ? '—' : ('Rp ' . number_format((float) $state, 0, ',', '.'))),
                        TextEntry::make('denda_dibayar')
                            ->label('Denda Dibayar')
                            ->formatStateUsing(fn ($state) => $state === null ? '—' : ('Rp ' . number_format((float) $state, 0, ',', '.'))),
                    ]),

                Section::make('Kondisi & Catatan')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('kondisi_buku')
                            ->label('Kondisi Buku')
                            ->badge()
                            ->colors([
                                'success' => 'baik',
                                'warning' => 'rusak',
                                'danger' => 'hilang',
                            ]),
                        TextEntry::make('catatan')
                            ->label('Catatan')
                            ->placeholder('—')
                            ->formatStateUsing(fn ($state) => blank($state) ? '—' : Str::of((string) $state)->trim()),
                    ]),

                Section::make('Diproses Oleh')
                    ->columns(1)
                    ->schema([
                        TextEntry::make('admin.nama_lengkap')
                            ->label('Admin')
                            ->placeholder('—')
                            ->formatStateUsing(function ($state, Pengembalian $record) {
                                return $state ?: ($record->admin?->username ?: '—');
                            }),
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
