<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Filament\Resources\Pages\EditRecord;

class EditSiswa extends EditRecord
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
            Actions\DeleteAction::make()
                ->action(function () {
                    /** @var Siswa $record */
                    $record = $this->record;

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
