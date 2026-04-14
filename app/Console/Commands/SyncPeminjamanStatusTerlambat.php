<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use Illuminate\Console\Command;

class SyncPeminjamanStatusTerlambat extends Command
{
    protected $signature = 'perpustakaan:sync-status-terlambat {--dry-run : Tampilkan jumlah perubahan tanpa update DB}';

    protected $description = "Normalisasi status peminjaman yang sudah dikembalikan agar berstatus 'dikembalikan' (termasuk yang pernah salah terset 'terlambat').";

    public function handle(): int
    {
        $query = Peminjaman::query()
            ->where('status', 'terlambat')
            ->whereNotNull('tanggal_kembali');

        $count = (clone $query)->count();

        if ($count === 0) {
            $this->info('Tidak ada data yang perlu disinkronkan.');
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->info("Dry run: {$count} peminjaman akan dinormalisasi statusnya menjadi 'dikembalikan'.");
            return self::SUCCESS;
        }

        $updated = $query->update([
            'status' => 'dikembalikan',
        ]);

        $this->info("Selesai: {$updated} peminjaman dinormalisasi menjadi 'dikembalikan'.");

        return self::SUCCESS;
    }
}
