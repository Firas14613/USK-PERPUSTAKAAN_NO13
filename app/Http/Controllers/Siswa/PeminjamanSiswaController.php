<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PeminjamanSiswaController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'buku_id' => ['required', 'integer', 'exists:buku,id'],
        ]);

        $siswa = $request->user()->siswa;

        if (! $siswa) {
            abort(403);
        }

        $maxPinjam = (int) config('perpustakaan.max_pinjam_siswa', 3);

        return DB::transaction(function () use ($validated, $siswa, $maxPinjam) {
            $buku = Buku::query()->lockForUpdate()->findOrFail($validated['buku_id']);

            if ($buku->stok < 1) {
                return back()->with('toast', [
                    'type' => 'error',
                    'message' => 'Stok buku sedang habis.',
                ]);
            }

            $aktifCount = Peminjaman::query()
                ->where('siswa_id', $siswa->id)
                ->whereIn('status', ['pending', 'dipinjam', 'terlambat'])
                ->count();

            if ($aktifCount >= $maxPinjam) {
                return back()->with('toast', [
                    'type' => 'error',
                    'message' => "Maksimal peminjaman aktif adalah {$maxPinjam} buku.",
                ]);
            }

            $sudahAda = Peminjaman::query()
                ->where('siswa_id', $siswa->id)
                ->where('buku_id', $buku->id)
                ->whereIn('status', ['pending', 'dipinjam', 'terlambat'])
                ->exists();

            if ($sudahAda) {
                return back()->with('toast', [
                    'type' => 'error',
                    'message' => 'Anda sudah memiliki peminjaman aktif untuk buku ini.',
                ]);
            }

            $kode = $this->generateKodePeminjaman();

            Peminjaman::create([
                'kode_peminjaman' => $kode,
                'siswa_id' => $siswa->id,
                'buku_id' => $buku->id,
                'admin_id' => null,
                'tanggal_pinjam' => now()->toDateString(),
                'tanggal_kembali' => null,
                'batas_pengembalian' => null,
                'status' => 'pending',
                'denda' => null,
                'catatan' => null,
            ]);

            return back()->with('toast', [
                'type' => 'success',
                'message' => 'Request peminjaman berhasil dibuat. Menunggu persetujuan admin.',
            ]);
        });
    }

    private function generateKodePeminjaman(): string
    {
        do {
            $kode = 'PMJ-'.Str::upper(Str::random(8));
        } while (Peminjaman::query()->where('kode_peminjaman', $kode)->exists());

        return $kode;
    }
}

