<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    protected $table = 'pengembalian';

    protected $fillable = [
        'peminjaman_id',
        'admin_id',
        'tanggal_kembali_aktual',
        'keterlambatan',
        'denda_per_hari',
        'denda_otomatis',
        'denda_dibayar',
        'kondisi_buku',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kembali_aktual' => 'date',
        ];
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
