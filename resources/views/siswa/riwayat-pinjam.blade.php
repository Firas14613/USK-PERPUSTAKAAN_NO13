@extends('layouts.siswa')

@section('title', 'Riwayat Pinjam')
@section('heading', 'Riwayat Peminjaman')

@section('content')
    <div class="mt-10 bg-surface-container-low rounded-xl overflow-hidden">
        <div class="px-8 py-6">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-outline">History Ledger</p>
            <h3 class="text-2xl font-black text-primary mt-2">Riwayat Peminjaman</h3>
            <p class="text-sm text-on-surface-variant mt-2">Data yang tampil hanya milik akun siswa yang sedang login.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-surface-container-high">
                    <tr>
                        <th class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest text-outline">Kode</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest text-outline">Buku</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest text-outline">Tgl Pinjam</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest text-outline">Batas</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest text-outline">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-surface-container-lowest">
                    @forelse ($riwayat as $row)
                        <tr class="hover:bg-surface-container-low transition-colors group">
                            <td class="px-8 py-5 text-sm font-mono text-primary font-semibold">{{ $row->kode_peminjaman }}</td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-14 bg-surface-container-highest rounded shadow-sm overflow-hidden flex-shrink-0">
                                        @if ($row->buku?->cover_image)
                                            <img alt="Cover" class="w-full h-full object-cover" src="{{ asset('storage/'.ltrim(str_replace('public/', '', $row->buku->cover_image), '/')) }}" />
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-on-surface leading-tight">{{ $row->buku?->judul ?? '-' }}</p>
                                        <p class="text-xs text-on-surface-variant mt-1">{{ $row->buku?->penulis ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm text-on-surface-variant">{{ optional($row->tanggal_pinjam)->format('d M Y') }}</td>
                            <td class="px-8 py-5 text-sm text-on-surface-variant">{{ $row->batas_pengembalian ? $row->batas_pengembalian->format('d M Y') : '—' }}</td>
                            <td class="px-8 py-5">
                                <x-siswa.status-badge :status="$row->status" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-10 text-sm text-on-surface-variant">Belum ada riwayat peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-8 py-4 border-t border-outline-variant/10 bg-surface-container-low/30">
            {{ $riwayat->links() }}
        </div>
    </div>
@endsection
