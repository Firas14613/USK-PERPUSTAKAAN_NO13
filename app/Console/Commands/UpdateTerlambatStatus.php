<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateTerlambatStatus extends Command
{
    protected $signature = 'peminjaman:update-terlambat';

    protected $description = 'Update status peminjaman yang sudah lewat batas pengembalian menjadi terlambat';

    public function handle()
    {
        $today = Carbon::today('Asia/Jakarta');

        $updated = Peminjaman::query()
            ->where('status', 'dipinjam')
            ->where('batas_pengembalian', '<', $today)
            ->update(['status' => 'terlambat']);

        $this->info("Berhasil update {$updated} peminjaman menjadi status terlambat.");

        return Command::SUCCESS;
    }
}
