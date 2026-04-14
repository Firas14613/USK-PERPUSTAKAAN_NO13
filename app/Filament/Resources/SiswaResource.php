<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Models\Peminjaman;
use App\Models\Siswa;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    public static function getModelLabel(): string
    {
        return 'Siswa';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Siswa';
    }

    protected static ?string $slug = 'siswa';

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Perpustakaan';

    protected static ?string $navigationLabel = 'Siswa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Akun')
                    ->schema([
                        TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->maxLength(255)
                            ->rule(fn (?Siswa $record) => Rule::unique('users', 'username')->ignore($record?->user_id)),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->rule(fn (?Siswa $record) => Rule::unique('users', 'email')->ignore($record?->user_id)),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn (?Siswa $record) => $record === null)
                            ->dehydrated(fn ($state) => filled($state)),
                        TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('telepon')
                            ->label('Telepon')
                            ->maxLength(20),
                    ])
                    ->columns(2),

                Section::make('Data Siswa')
                    ->schema([
                        TextInput::make('nis')
                            ->label('NIS')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        TextInput::make('kelas')
                            ->label('Kelas')
                            ->required()
                            ->maxLength(20),
                        TextInput::make('jurusan')
                            ->label('Jurusan')
                            ->maxLength(100),
                        DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->maxDate(now())
                            ->rule('before_or_equal:today'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'aktif' => 'Aktif',
                                'lulus' => 'Lulus',
                                'keluar' => 'Keluar',
                            ])
                            ->default('aktif')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.nama_lengkap')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kelas')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jurusan')
                    ->label('Jurusan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'aktif',
                        'gray' => 'lulus',
                        'danger' => 'keluar',
                    ])
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'lulus' => 'Lulus',
                        'keluar' => 'Keluar',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('nonaktifkan')
                    ->label('Nonaktifkan')
                    ->icon('heroicon-o-lock-closed')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Nonaktifkan Akun')
                    ->modalDescription('Akun siswa akan dinonaktifkan dan tidak dapat login lagi.')
                    ->visible(fn (Siswa $record) => $record->status !== 'keluar')
                    ->disabled(fn (Siswa $record) => $record->hasPeminjamanBerjalan())
                    ->tooltip(fn (Siswa $record) => $record->hasPeminjamanBerjalan() ? 'Tidak bisa dinonaktifkan: masih ada peminjaman dipinjam/terlambat. Selesaikan pengembalian dulu.' : null)
                    ->action(function (Siswa $record) {
                        if ($record->hasPeminjamanBerjalan()) {
                            Notification::make()
                                ->danger()
                                ->title('Tidak bisa dinonaktifkan')
                                ->body('Masih ada peminjaman berstatus dipinjam/terlambat. Selesaikan pengembalian dulu.')
                                ->send();

                            return;
                        }

                        $pendingCount = $record->countPendingPeminjaman();

                        DB::transaction(function () use ($record) {
                            Peminjaman::query()
                                ->where('siswa_id', $record->id)
                                ->where('status', 'pending')
                                ->update([
                                    'admin_id' => auth()->id(),
                                    'status' => 'ditolak',
                                    'catatan' => DB::raw("CASE WHEN catatan IS NULL OR catatan = '' THEN 'Akun dinonaktifkan' ELSE CONCAT(catatan, '\nAkun dinonaktifkan') END"),
                                ]);

                            $record->update(['status' => 'keluar']);
                        });

                        Notification::make()
                            ->success()
                            ->title('Akun dinonaktifkan')
                            ->body($pendingCount > 0
                                ? "Siswa tidak dapat login. {$pendingCount} request pending otomatis ditolak."
                                : 'Siswa tidak dapat login.')
                            ->send();
                    }),

                Tables\Actions\Action::make('aktifkan')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aktifkan Akun')
                    ->modalDescription('Akun siswa akan diaktifkan kembali dan bisa login.')
                    ->visible(fn (Siswa $record) => $record->status === 'keluar')
                    ->action(function (Siswa $record) {
                        $record->update(['status' => 'aktif']);

                        Notification::make()
                            ->success()
                            ->title('Akun diaktifkan')
                            ->body('Siswa dapat login kembali.')
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('bulkNonaktifkan')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-lock-closed')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            /** @var array<int> $selectedIds */
                            $selectedIds = $records
                                ->filter(fn ($r) => $r instanceof Siswa)
                                ->pluck('id')
                                ->map(fn ($id) => (int) $id)
                                ->all();

                            if (empty($selectedIds)) {
                                Notification::make()
                                    ->warning()
                                    ->title('Tidak ada data')
                                    ->body('Tidak ada siswa yang dipilih.')
                                    ->send();

                                return;
                            }

                            $alreadyKeluarIds = Siswa::query()
                                ->whereKey($selectedIds)
                                ->where('status', 'keluar')
                                ->pluck('id')
                                ->map(fn ($id) => (int) $id)
                                ->all();

                            $blockedBorrowingIds = Siswa::query()
                                ->whereKey($selectedIds)
                                ->whereHas('peminjaman', fn ($q) => $q->whereIn('status', ['dipinjam', 'terlambat']))
                                ->pluck('id')
                                ->map(fn ($id) => (int) $id)
                                ->all();

                            $skipIds = array_values(array_unique(array_merge($alreadyKeluarIds, $blockedBorrowingIds)));
                            $canDisableIds = array_values(array_diff($selectedIds, $skipIds));

                            $pendingRejected = 0;

                            if (! empty($canDisableIds)) {
                                DB::transaction(function () use ($canDisableIds, &$pendingRejected) {
                                    $pendingRejected = (int) Peminjaman::query()
                                        ->whereIn('siswa_id', $canDisableIds)
                                        ->where('status', 'pending')
                                        ->count();

                                    Peminjaman::query()
                                        ->whereIn('siswa_id', $canDisableIds)
                                        ->where('status', 'pending')
                                        ->update([
                                            'admin_id' => auth()->id(),
                                            'status' => 'ditolak',
                                            'catatan' => DB::raw("CASE WHEN catatan IS NULL OR catatan = '' THEN 'Akun dinonaktifkan' ELSE CONCAT(catatan, '\nAkun dinonaktifkan') END"),
                                        ]);

                                    Siswa::query()
                                        ->whereKey($canDisableIds)
                                        ->update(['status' => 'keluar']);
                                });
                            }

                            $disabledCount = count($canDisableIds);
                            $blockedCount = count($blockedBorrowingIds);

                            $message = "Berhasil menonaktifkan {$disabledCount} akun.";
                            if ($pendingRejected > 0) {
                                $message .= " {$pendingRejected} request pending otomatis ditolak.";
                            }
                            if ($blockedCount > 0) {
                                $message .= " {$blockedCount} akun dilewati karena masih meminjam (dipinjam/terlambat).";
                            }

                            Notification::make()
                                ->success()
                                ->title('Nonaktifkan selesai')
                                ->body($message)
                                ->send();
                        }),

                    BulkAction::make('bulkAktifkan')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $targets = $records->where('status', '=', 'keluar');
                            $count = $targets->count();

                            if ($count > 0) {
                                Siswa::query()
                                    ->whereKey($targets->pluck('id')->all())
                                    ->update(['status' => 'aktif']);
                            }

                            Notification::make()
                                ->success()
                                ->title('Aktifkan berhasil')
                                ->body("Berhasil mengaktifkan {$count} akun.")
                                ->send();
                        }),

                    BulkAction::make('deleteSafely')
                        ->label('Hapus Aman')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Akun (Aman)')
                        ->modalDescription('Akun dengan peminjaman aktif atau riwayat denda tidak akan dihapus.')
                        ->action(function (Collection $records) {
                            $deleted = 0;
                            $blocked = 0;
                            $exampleReason = null;

                            foreach ($records as $record) {
                                if (! $record instanceof Siswa) {
                                    continue;
                                }

                                $reason = $record->getDeletionBlockReason();
                                if ($reason !== null) {
                                    $blocked++;
                                    $exampleReason ??= $reason;
                                    continue;
                                }

                                DB::transaction(function () use ($record, &$deleted) {
                                    $record->refresh();
                                    $user = $record->user;
                                    $record->delete();
                                    $user?->delete();
                                    $deleted++;
                                });
                            }

                            $message = "Berhasil menghapus {$deleted} akun.";
                            if ($blocked > 0) {
                                $message .= " {$blocked} akun dilewati.";
                            }

                            if ($blocked > 0 && $exampleReason) {
                                $message .= " Contoh alasan: {$exampleReason}";
                            }

                            Notification::make()
                                ->success()
                                ->title('Hapus aman selesai')
                                ->body($message)
                                ->send();
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
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}
