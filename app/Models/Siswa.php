<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DomainException;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'nis',
        'kelas',
        'jurusan',
        'tanggal_lahir',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'siswa_id');
    }

    public function hasPeminjamanAktif(): bool
    {
        return $this->peminjaman()
            ->whereIn('status', ['pending', 'dipinjam', 'terlambat'])
            ->exists();
    }

    public function hasPeminjamanBerjalan(): bool
    {
        return $this->peminjaman()
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->exists();
    }

    public function countPendingPeminjaman(): int
    {
        return (int) $this->peminjaman()
            ->where('status', 'pending')
            ->count();
    }

    public function hasRiwayatDenda(): bool
    {
        return $this->peminjaman()
            ->whereNotNull('denda')
            ->where('denda', '>', 0)
            ->exists();
    }

    public function getDeletionBlockReason(): ?string
    {
        if ($this->hasPeminjamanAktif()) {
            return 'Tidak bisa menghapus akun karena masih ada peminjaman aktif (pending/dipinjam/terlambat).';
        }

        if ($this->hasRiwayatDenda()) {
            return 'Tidak bisa menghapus akun karena memiliki riwayat denda.';
        }

        return null;
    }

    protected static function booted(): void
    {
        static::deleting(function (self $siswa) {
            $reason = $siswa->getDeletionBlockReason();

            if ($reason !== null) {
                throw new DomainException($reason);
            }
        });
    }
}
