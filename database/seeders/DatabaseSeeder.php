<?php

namespace Database\Seeders;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@perpus.sch.id'],
            [
                'username' => 'admin',
                'role' => 'admin',
                'nama_lengkap' => 'Admin Perpustakaan',
                'alamat' => null,
                'telepon' => null,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $userSiswa = User::query()->firstOrCreate(
            ['email' => 'siswa1@perpus.sch.id'],
            [
                'username' => 'siswa1',
                'role' => 'siswa',
                'nama_lengkap' => 'Siswa 1',
                'alamat' => 'Purwokerto',
                'telepon' => '081234567890',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        Siswa::query()->firstOrCreate(
            ['user_id' => $userSiswa->id],
            [
                'nis' => '2021081542',
                'kelas' => 'XII RPL 1',
                'jurusan' => 'Rekayasa Perangkat Lunak',
                'tanggal_lahir' => '2005-08-15',
                'status' => 'aktif',
            ],
        );
    }
}
