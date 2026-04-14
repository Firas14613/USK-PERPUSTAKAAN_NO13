<!doctype html>
<html lang="id">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Laporan Sirkulasi</title>
        <style>
            * { box-sizing: border-box; }
            body {
                font-family: DejaVu Sans, Arial, sans-serif;
                font-size: 12px;
                color: #191c1e;
                margin: 24px;
            }
            h1 {
                font-size: 20px;
                margin: 0 0 4px 0;
                color: #00236f;
                letter-spacing: -0.2px;
            }
            .meta {
                font-size: 10px;
                color: #444651;
                margin-bottom: 16px;
            }
            .summary {
                width: 100%;
                border: 1px solid rgba(117, 118, 130, 0.25);
                border-radius: 10px;
                padding: 12px;
                margin: 14px 0 18px 0;
                background: #f7f9fb;
            }
            .summary-grid {
                width: 100%;
                border-collapse: collapse;
            }
            .summary-grid td {
                padding: 6px 8px;
                vertical-align: top;
            }
            .k {
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #444651;
                font-weight: 700;
                margin-bottom: 2px;
            }
            .v {
                font-size: 16px;
                font-weight: 800;
                color: #191c1e;
            }
            table.report {
                width: 100%;
                border-collapse: collapse;
            }
            table.report thead th {
                background: #e6e8ea;
                color: #444651;
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 1px;
                text-align: left;
                padding: 10px 8px;
                border-bottom: 1px solid rgba(117, 118, 130, 0.25);
            }
            table.report tbody td {
                padding: 10px 8px;
                border-bottom: 1px solid rgba(117, 118, 130, 0.18);
                vertical-align: top;
            }
            .mono { font-family: DejaVu Sans Mono, monospace; font-size: 11px; }
            .muted { color: #444651; }
            .right { text-align: right; }
            .badge {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 9999px;
                font-size: 9px;
                font-weight: 800;
                letter-spacing: 1px;
                text-transform: uppercase;
                background: rgba(25, 28, 30, 0.06);
                color: #444651;
            }
            .badge--pending { background: #ffdbcb; color: #341100; }
            .badge--dipinjam { background: #dce1ff; color: #00164e; }
            .badge--dikembalikan { background: #d3e4fe; color: #0b1c30; }
            .badge--terlambat, .badge--hilang { background: #ffdad6; color: #93000a; }
            .badge--ditolak { background: rgba(25, 28, 30, 0.08); color: #444651; }
            .small { font-size: 10px; }
        </style>
    </head>
    <body>
        <h1>Laporan Sirkulasi</h1>
        <div class="meta">
            Dicetak: {{ $generatedAt?->format('d M Y H:i') }} WIB
            &nbsp;•&nbsp; Admin: {{ $adminName ?? '-' }}
            @php
                $filterInfo = $filterInfo ?? [];
                $hasDate = !empty($filterInfo['dateFromLabel']) || !empty($filterInfo['dateToLabel']);
                $dateFieldLabel = $filterInfo['dateFieldLabel'] ?? 'Tanggal';
                $status = $filterInfo['status'] ?? null;
                $search = $filterInfo['search'] ?? null;

                $statusLabel = match ((string) $status) {
                    'pending' => 'Pending',
                    'dipinjam' => 'Dipinjam',
                    'dikembalikan' => 'Dikembalikan',
                    'terlambat' => 'Terlambat',
                    'hilang' => 'Hilang',
                    'ditolak' => 'Ditolak',
                    default => null,
                };
            @endphp
            <br />
            Filter: {{ $dateFieldLabel }} =
            @if ($hasDate)
                {{ $filterInfo['dateFromLabel'] ?? '—' }} s/d {{ $filterInfo['dateToLabel'] ?? '—' }}
            @else
                Semua tanggal
            @endif
            @if ($statusLabel)
                &nbsp;•&nbsp; Status: {{ $statusLabel }}
            @endif
            @if ($search)
                &nbsp;•&nbsp; Pencarian: "{{ $search }}"
            @endif
        </div>

        <div class="summary">
            <table class="summary-grid">
                <tr>
                    <td>
                        <div class="k">Total Data</div>
                        <div class="v">{{ number_format((int) ($totalRows ?? 0), 0, ',', '.') }}</div>
                    </td>
                    <td>
                        <div class="k">Total Denda</div>
                        <div class="v">Rp {{ number_format((float) ($totalDenda ?? 0), 0, ',', '.') }}</div>
                    </td>
                    <td>
                        <div class="k">Terlambat</div>
                        <div class="v">{{ number_format((int) ($totalTerlambat ?? 0), 0, ',', '.') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="report">
            <thead>
                <tr>
                    <th style="width: 20%;">Peminjam</th>
                    <th style="width: 30%;">Judul Buku</th>
                    <th style="width: 12%;">Tgl Pinjam</th>
                    <th style="width: 12%;">Tgl Kembali</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 14%;" class="right">Denda</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $row)
                    @php
                        $status = (string) ($row->status ?? '');
                        $badge = match ($status) {
                            'pending' => 'badge--pending',
                            'dipinjam' => 'badge--dipinjam',
                            'dikembalikan' => 'badge--dikembalikan',
                            'terlambat' => 'badge--terlambat',
                            'hilang' => 'badge--hilang',
                            'ditolak' => 'badge--ditolak',
                            default => '',
                        };
                        $statusLabel = match ($status) {
                            'pending' => 'Pending',
                            'dipinjam' => 'Dipinjam',
                            'dikembalikan' => 'Dikembalikan',
                            'terlambat' => 'Terlambat',
                            'hilang' => 'Hilang',
                            'ditolak' => 'Ditolak',
                            default => ucfirst($status),
                        };
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight: 700;">{{ $row->siswa?->user?->nama_lengkap ?? '-' }}</div>
                            <div class="small muted mono">{{ $row->kode_peminjaman ?? '' }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 700;">{{ $row->buku?->judul ?? '-' }}</div>
                            <div class="small muted">{{ $row->buku?->penulis ?? '' }}</div>
                        </td>
                        <td class="muted">
                            {{ $row->tanggal_pinjam ? $row->tanggal_pinjam->format('d M Y') : '—' }}
                        </td>
                        <td class="muted">
                            {{ $row->tanggal_kembali ? $row->tanggal_kembali->format('d M Y') : '—' }}
                        </td>
                        <td>
                            <span class="badge {{ $badge }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="right">
                            @if (!empty($row->denda) && (float) $row->denda > 0)
                                Rp {{ number_format((float) $row->denda, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>
