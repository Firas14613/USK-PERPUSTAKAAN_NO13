<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Buku extends Model
{
    use SoftDeletes;

    protected $table = 'buku';

    protected $fillable = [
        'kode_buku',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'kategori_id',
        'isbn',
        'jumlah_halaman',
        'deskripsi',
        'stok',
        'lokasi_rak',
        'cover_image',
    ];

    protected $appends = ['cover_url'];

    public function getCoverUrlAttribute(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        $path = ltrim(str_replace('public/', '', $this->cover_image), '/');

        return Storage::disk('public')->url($path);
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriBuku::class, 'kategori_id');
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'buku_id');
    }
}
