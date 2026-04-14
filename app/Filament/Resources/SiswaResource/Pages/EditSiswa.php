<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Peminjaman;
use App\Models\Siswa;
use Filament\Actions;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Filament\Resources\Pages\EditRecordRedirectToList;

class EditSiswa extends EditRecordRedirectToList
{
    protected static string $resource = SiswaResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Siswa $record */
        $record = $this->record;
        $user = $record->user;

        return array_merge($data, [
            'username' => $user?->username,
            'email' => $user?->email,
            'nama_lengkap' => $user?->nama_lengkap,
            'alamat' => $user?->alamat,
            'telepon' => $user?->telepon,
        ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Siswa $record */
        return DB::transaction(function () use ($record, $data) {
            $user = $record->user;

            if ($user) {
                $user->fill([
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'role' => 'siswa',
                    'nama_lengkap' => $data['nama_lengkap'],
                    'alamat' => $data['alamat'] ?? null,
                    'telepon' => $data['telepon'] ?? null,
                ]);

                if (! empty($data['password'] ?? null)) {
                    $user->password = Hash::make($data['password']);
                }

                $user->save();
            }

            $record->update([
                'nis' => $data['nis'],
                'kelas' => $data['kelas'],
                'jurusan' => $data['jurusan'] ?? null,
                'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
                'status' => $data['status'] ?? 'aktif',
            ]);

            return $record;
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('nonaktifkan')
                ->label('Nonaktifkan')
                ->icon('heroicon-o-lock-closed')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Nonaktifkan Akun')
                ->modalDescription('Akun siswa akan dinonaktifkan dan tidak dapat login lagi.')
                ->visible(fn () => ($this->record?->status ?? null) !== 'keluar')
                ->disabled(fn () => (bool) ($this->record?->hasPeminjamanBerjalan() ?? false))
                ->tooltip(fn () => ($this->record?->hasPeminjamanBerjalan() ?? false) ? 'Tidak bisa dinonaktifkan: masih ada peminjaman dipinjam/terlambat. Selesaikan pengembalian dulu.' : null)
                ->action(function () {
                    /** @var Siswa $record */
                    $record = $this->record;

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

            Actions\Action::make('aktifkan')
                ->label('Aktifkan')
                ->icon('heroicon-o-lock-open')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Aktifkan Akun')
                ->modalDescription('Akun siswa akan diaktifkan kembali dan bisa login.')
                ->visible(fn () => ($this->record?->status ?? null) === 'keluar')
                ->action(function () {
                    /** @var Siswa $record */
                    $record = $this->record;

                    $record->update(['status' => 'aktif']);

                    Notification::make()
                        ->success()
                        ->title('Akun diaktifkan')
                        ->body('Siswa dapat login kembali.')
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->disabled(fn () => (bool) $this->record?->getDeletionBlockReason())
                ->tooltip(fn () => $this->record?->getDeletionBlockReason())
                ->action(function () {
                    /** @var Siswa $record */
                    $record = $this->record;

                    $reason = $record->getDeletionBlockReason();
                    if ($reason !== null) {
                        Notification::make()
                            ->danger()
                            ->title('Tidak bisa menghapus akun')
                            ->body($reason)
                            ->send();

                        return;
                    }

                    DB::transaction(function () use ($record) {
                        $user = $record->user;
                        $record->delete();
                        $user?->delete();
                    });

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}
