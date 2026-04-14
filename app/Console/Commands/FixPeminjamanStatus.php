<?php

namespace App\Console\Commands;

use App\Models\Pengembalian;
use Illuminate\Console\Command;

class FixPeminjamanStatus extends Command
{
    protected $signature = 'peminjaman:fix-status';

    protected $description = 'Fix status peminjaman yang terlambat tapi statusnya masih dikembalikan';

    public function handle(): int
    {
        $pengembalians = Pengembalian::query()
            ->where('keterlambatan', '>', 0)
            ->with('peminjaman')
            ->get();

        $fixed = 0;

        foreach ($pengembalians as $p) {
            if ($p->peminjaman && $p->peminjaman->status === 'dikembalikan') {
                $p->peminjaman->update(['status' => 'terlambat']);
                $fixed++;
                $this->info("Fixed: {$p->peminjaman->kode_peminjaman} ({$p->keterlambatan} hari terlambat)");
            }
        }

        $this->info("Total fixed: {$fixed} records");

        return Command::SUCCESS;
    }
}
