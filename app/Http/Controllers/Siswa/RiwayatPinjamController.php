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

        $riwayat = Peminjaman::query()
            ->with(['buku.kategori'])
            ->where('siswa_id', $siswaId)
            ->latest('created_at')
            ->paginate(10);

        return view('siswa.riwayat-pinjam', [
            'riwayat' => $riwayat,
        ]);
    }
}

