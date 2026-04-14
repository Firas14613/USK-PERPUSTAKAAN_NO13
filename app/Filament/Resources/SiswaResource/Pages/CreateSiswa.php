<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use App\Models\User;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Filament\Resources\Pages\CreateRecord;

class CreateSiswa extends CreateRecord
{
    protected static string $resource = SiswaResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'role' => 'siswa',
                'nama_lengkap' => $data['nama_lengkap'],
                'alamat' => $data['alamat'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
            ]);

            return Siswa::create([
                'user_id' => $user->id,
                'nis' => $data['nis'],
                'kelas' => $data['kelas'],
                'jurusan' => $data['jurusan'] ?? null,
                'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
                'status' => $data['status'] ?? 'aktif',
            ]);
        });
    }
}
