<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LaporanPdfExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $search = trim((string) $request->query('tableSearch', ''));
        $filters = (array) $request->query('tableFilters', []);
        $status = data_get($filters, 'status.value');
        $dateFrom = data_get($filters, 'rentang_tanggal.from');
        $dateTo = data_get($filters, 'rentang_tanggal.to');

        $sortColumn = (string) $request->query('tableSortColumn', 'created_at');
        $sortDirection = strtolower((string) $request->query('tableSortDirection', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSortColumns = [
            'created_at',
            'tanggal_pinjam',
            'tanggal_kembali',
            'status',
            'denda',
            'siswa.user.nama_lengkap',
            'buku.judul',
        ];

        if (! in_array($sortColumn, $allowedSortColumns, true)) {
            $sortColumn = 'created_at';
        }

        /** @var Builder $query */
        $query = Peminjaman::query()
            ->select('peminjaman.*')
            ->with(['siswa.user', 'buku']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('tanggal_pinjam', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('tanggal_pinjam', '<=', $dateTo);
        }

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search) {
                $query
                    ->whereHas('siswa.user', function (Builder $query) use ($search) {
                        $query
                            ->where('nama_lengkap', 'like', '%' . $search . '%')
                            ->orWhere('username', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('buku', function (Builder $query) use ($search) {
                        $query
                            ->where('judul', 'like', '%' . $search . '%')
                            ->orWhere('penulis', 'like', '%' . $search . '%');
                    })
                    ->orWhere('kode_peminjaman', 'like', '%' . $search . '%');
            });
        }

        if ($sortColumn === 'siswa.user.nama_lengkap') {
            $query
                ->leftJoin('siswa', 'siswa.id', '=', 'peminjaman.siswa_id')
                ->leftJoin('users as siswa_user', 'siswa_user.id', '=', 'siswa.user_id')
                ->orderBy('siswa_user.nama_lengkap', $sortDirection);
        } elseif ($sortColumn === 'buku.judul') {
            $query
                ->leftJoin('buku', 'buku.id', '=', 'peminjaman.buku_id')
                ->orderBy('buku.judul', $sortDirection);
        } else {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $records = (clone $query)->get();

        $totalRows = (clone $query)->count();
        $totalDenda = (float) ((clone $query)->sum('denda') ?? 0);
        $totalTerlambat = (clone $query)->where('status', 'terlambat')->count();

        $generatedAt = now()->timezone('Asia/Jakarta');
        $admin = $request->user();

        $dateFromLabel = null;
        $dateToLabel = null;

        try {
            $dateFromLabel = $dateFrom ? Carbon::parse($dateFrom)->format('d M Y') : null;
        } catch (\Throwable) {
            $dateFromLabel = null;
        }

        try {
            $dateToLabel = $dateTo ? Carbon::parse($dateTo)->format('d M Y') : null;
        } catch (\Throwable) {
            $dateToLabel = null;
        }

        $pdf = Pdf::loadView('pdf.laporan-sirkulasi', [
            'records' => $records,
            'totalRows' => $totalRows,
            'totalDenda' => $totalDenda,
            'totalTerlambat' => $totalTerlambat,
            'generatedAt' => $generatedAt,
            'adminName' => $admin?->nama_lengkap ?? $admin?->username ?? '-',
            'filterInfo' => [
                'dateFieldLabel' => 'Tanggal Pinjam',
                'dateFromLabel' => $dateFromLabel,
                'dateToLabel' => $dateToLabel,
                'status' => $status ?: null,
                'search' => $search !== '' ? $search : null,
            ],
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-sirkulasi_' . $generatedAt->format('Y-m-d_H-i') . '.pdf';

        return $pdf->download($filename);
    }
}
