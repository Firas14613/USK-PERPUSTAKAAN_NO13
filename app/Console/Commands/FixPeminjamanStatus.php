<?php

namespace App\Console\Commands;

use App\Models\Pengembalian;
use Illuminate\Console\Command;

class FixPeminjamanStatus extends Command
{
    protected $signature = 'peminjaman:fix-status';

    protected $description = "Normalisasi status peminjaman yang sudah dikembalikan (termasuk terlambat) agar berstatus 'dikembalikan'.";

    public function handle(): int
    {
        $pengembalians = Pengembalian::query()
            ->where('keterlambatan', '>', 0)
            ->with('peminjaman')
            ->get();

        $fixed = 0;

        foreach ($pengembalians as $p) {
            if (! $p->peminjaman) {
                continue;
            }

            $targetStatus = $p->kondisi_buku === 'hilang' ? 'hilang' : 'dikembalikan';

            if ($p->peminjaman->status !== $targetStatus) {
                $p->peminjaman->update([
                    'status' => $targetStatus,
                ]);

                $fixed++;
                $this->info("Fixed: {$p->peminjaman->kode_peminjaman} -> {$targetStatus} (keterlambatan {$p->keterlambatan} hari)");
            }
        }

        $this->info("Total fixed: {$fixed} records");

        return Command::SUCCESS;
    }
}
