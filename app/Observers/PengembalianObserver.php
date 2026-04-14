<?php

namespace App\Observers;

use App\Models\Pengembalian;

class PengembalianObserver
{
    public function created(Pengembalian $pengembalian): void
    {
        $peminjaman = $pengembalian->peminjaman;

        if (! $peminjaman) {
            return;
        }

        if ($pengembalian->kondisi_buku === 'hilang') {
            if ($peminjaman->status !== 'hilang') {
                $peminjaman->update(['status' => 'hilang']);
            }

            return;
        }

        if (($pengembalian->keterlambatan ?? 0) > 0) {
            if ($peminjaman->status !== 'terlambat') {
                $peminjaman->update(['status' => 'terlambat']);
            }

            return;
        }

        if ($peminjaman->status !== 'dikembalikan') {
            $peminjaman->update(['status' => 'dikembalikan']);
        }
    }
}

