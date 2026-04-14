<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\KategoriBuku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KoleksiBukuController extends Controller
{
    public function index(Request $request): View
    {
        $kategoriId = $request->integer('kategori_id') ?: null;
        $search = trim((string) $request->string('q'));
        $siswaId = $request->user()->siswa?->id;

        $kategori = KategoriBuku::query()
            ->orderBy('nama_kategori')
            ->get();

        $buku = Buku::query()
            ->with('kategori')
            ->when($kategoriId, fn ($q) => $q->where('kategori_id', $kategoriId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('judul', 'like', '%' . $search . '%')
                        ->orWhere('penulis', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('judul')
            ->paginate(12)
            ->withQueryString();

        $aktifByBukuId = collect();

        if ($siswaId) {
            $aktifByBukuId = Peminjaman::query()
                ->select('buku_id')
                ->where('siswa_id', $siswaId)
                ->whereIn('status', ['pending', 'dipinjam', 'terlambat'])
                ->whereIn('buku_id', $buku->pluck('id')->all())
                ->groupBy('buku_id')
                ->pluck('buku_id')
                ->flip();
        }

        return view('siswa.koleksi-buku', [
            'kategori' => $kategori,
            'buku' => $buku,
            'kategoriId' => $kategoriId,
            'search' => $search,
            'aktifByBukuId' => $aktifByBukuId,
        ]);
    }
}
