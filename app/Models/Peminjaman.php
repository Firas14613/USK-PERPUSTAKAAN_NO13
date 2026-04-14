<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'kode_peminjaman',
        'siswa_id',
        'buku_id',
        'admin_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'batas_pengembalian',
        'status',
        'denda',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_kembali' => 'date',
            'batas_pengembalian' => 'date',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function pengembalian()
    {
        return $this->hasMany(Pengembalian::class, 'peminjaman_id');
    }
}
