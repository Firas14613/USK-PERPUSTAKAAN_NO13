<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $siswaId = $request->user()->siswa?->id;

        $sedangDipinjam = Peminjaman::query()
            ->with(['buku.kategori'])
            ->where('siswa_id', $siswaId)
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->latest('tanggal_pinjam')
            ->limit(5)
            ->get();

        return view('siswa.dashboard', [
            'sedangDipinjam' => $sedangDipinjam,
        ]);
    }
}

