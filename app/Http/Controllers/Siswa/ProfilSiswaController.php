<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Siswa\ProfilUpdateRequest;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfilSiswaController extends Controller
{
    public function edit(Request $request): View
    {
        $siswa = $request->user()->siswa;

        $bukuDipinjam = 0;
        $jatuhTempo = 0;

        if ($siswa) {
            $bukuDipinjam = Peminjaman::query()
                ->where('siswa_id', $siswa->id)
                ->whereIn('status', ['dipinjam', 'terlambat'])
                ->count();

            $jatuhTempo = Peminjaman::query()
                ->where('siswa_id', $siswa->id)
                ->whereIn('status', ['dipinjam', 'terlambat'])
                ->whereNotNull('batas_pengembalian')
                ->whereDate('batas_pengembalian', '<=', now()->toDateString())
                ->count();
        }

        return view('siswa.profil', [
            'user' => $request->user(),
            'siswa' => $siswa,
            'bukuDipinjam' => $bukuDipinjam,
            'jatuhTempo' => $jatuhTempo,
        ]);
    }

    public function update(ProfilUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated())->save();

        return Redirect::route('siswa.profil.edit')->with('toast', [
            'type' => 'success',
            'message' => 'Profil berhasil diperbarui.',
        ]);
    }
}

