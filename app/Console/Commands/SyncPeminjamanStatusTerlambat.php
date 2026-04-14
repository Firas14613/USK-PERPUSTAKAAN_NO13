<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use Illuminate\Console\Command;

class SyncPeminjamanStatusTerlambat extends Command
{
    protected $signature = 'perpustakaan:sync-status-terlambat {--dry-run : Tampilkan jumlah perubahan tanpa update DB}';

    protected $description = "Set status peminjaman menjadi 'terlambat' jika ada pengembalian dengan keterlambatan > 0 (untuk data lama yang masih 'dikembalikan').";

    public function handle(): int
    {
        $query = Peminjaman::query()
            ->where('status', 'dikembalikan')
            ->whereHas('pengembalian', fn ($q) => $q->where('keterlambatan', '>', 0));

        $count = (clone $query)->count();

        if ($count === 0) {
            $this->info('Tidak ada data yang perlu disinkronkan.');
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->info("Dry run: {$count} peminjaman akan diubah statusnya menjadi 'terlambat'.");
            return self::SUCCESS;
        }

        $updated = $query->update([
            'status' => 'terlambat',
        ]);

        $this->info("Selesai: {$updated} peminjaman diubah statusnya menjadi 'terlambat'.");

        return self::SUCCESS;
    }
}

