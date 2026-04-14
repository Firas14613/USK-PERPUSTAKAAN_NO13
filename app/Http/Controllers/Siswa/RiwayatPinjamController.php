<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiwayatPinjamController extends Controller
{
    public function index(Request $request): View
    {
        $siswaId = $request->user()->siswa?->id;

        $dendaPerHari = (int) config('perpustakaan.denda_per_hari', 1000);
        $today = now()->toDateString();

        $totalPeminjaman = Peminjaman::query()
            ->where('siswa_id', $siswaId)
            ->count();

        $sedangDipinjam = Peminjaman::query()
            ->where('siswa_id', $siswaId)
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->count();

        $riwayat = Peminjaman::query()
            ->with([
                'buku.kategori',
                'pengembalian' => fn ($q) => $q->latest('tanggal_kembali_aktual'),
            ])
            ->where('siswa_id', $siswaId)
            ->latest('created_at')
            ->paginate(10);

        return view('siswa.riwayat-pinjam', [
            'riwayat' => $riwayat,
            'totalPeminjaman' => $totalPeminjaman,
            'sedangDipinjam' => $sedangDipinjam,
            'dendaPerHari' => $dendaPerHari,
            'today' => $today,
        ]);
    }
}
