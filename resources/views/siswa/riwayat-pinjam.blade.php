@extends('layouts.siswa')

@section('title', 'Riwayat Pinjam')
@section('heading', 'Riwayat Peminjaman')

@section('content')
    <div class="mt-10" x-data="{
        modalOpen: false,
        selected: null,
        dendaPerHari: {{ (int) ($dendaPerHari ?? 1000) }},
        today: @js($today ?? now()->toDateString()),
        openDetail(payload) {
            this.selected = payload;
            this.modalOpen = true;
            document.body.style.overflow = 'hidden';
        },
        closeDetail() {
            this.modalOpen = false;
            this.selected = null;
            document.body.style.overflow = '';
        },
        fmtDate(dateStr) {
            if (!dateStr) return '—';
            if (!this.isReasonableDate(dateStr)) return '—';
            try {
                const d = new Date(dateStr + 'T00:00:00');
                return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            } catch (e) {
                return dateStr;
            }
        },
        isReasonableDate(dateStr) {
            if (!dateStr || typeof dateStr !== 'string') return false;
            const year = Number(dateStr.slice(0, 4));
            if (!Number.isFinite(year)) return false;
            return year >= 2000 && year <= 2100;
        },
        fmtRupiah(num) {
            const n = Number(num || 0);
            return 'Rp ' + n.toLocaleString('id-ID');
        },
        statusLabel(status) {
            const map = {
                pending: 'PENDING',
                dipinjam: 'DIPINJAM',
                terlambat: 'TERLAMBAT',
                dikembalikan: 'DIKEMBALIKAN',
                ditolak: 'DITOLAK',
                hilang: 'HILANG',
            };
            return map[status] || String(status || '').toUpperCase();
        },
        statusClass(status) {
            if (status === 'pending') return 'bg-tertiary-fixed text-on-tertiary-fixed';
            if (status === 'dipinjam') return 'bg-primary-fixed text-on-primary-fixed';
            if (status === 'terlambat') return 'bg-error-container text-on-error-container';
            if (status === 'dikembalikan') return 'bg-secondary-fixed text-on-secondary-fixed';
            if (status === 'hilang') return 'bg-error-container text-on-error-container';
            if (status === 'ditolak') return 'bg-surface-container-high text-on-surface-variant';
            return 'bg-surface-container-high text-on-surface-variant';
        },
        kondisiLabel(kondisi) {
            const map = { baik: 'BAIK', rusak: 'RUSAK', hilang: 'HILANG' };
            return map[kondisi] || (kondisi ? String(kondisi).toUpperCase() : '—');
        },
        kondisiClass(kondisi) {
            if (kondisi === 'baik') return 'bg-secondary-fixed text-on-secondary-fixed';
            if (kondisi === 'rusak') return 'bg-tertiary-fixed text-on-tertiary-fixed';
            if (kondisi === 'hilang') return 'bg-error-container text-on-error-container';
            return 'bg-surface-container-high text-on-surface-variant';
        },
        overdueDays() {
            if (!this.selected?.batas_pengembalian) return 0;
            if (!this.isReasonableDate(this.selected.batas_pengembalian)) return 0;
            const due = new Date(this.selected.batas_pengembalian + 'T00:00:00');
            const today = new Date(this.today + 'T00:00:00');
            const diff = Math.floor((today - due) / (1000 * 60 * 60 * 24));
            return Math.max(0, diff);
        },
	    }" @keydown.escape.window="closeDetail()">
	        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-10 mb-10">
	            <div class="editorial-gradient p-6 rounded-xl flex flex-col justify-between overflow-hidden relative shadow-ambient">
	                <div class="flex justify-between items-start">
	                    <span class="material-symbols-outlined text-white/90 text-3xl">auto_stories</span>
	                    <span class="text-[10px] font-bold uppercase tracking-widest text-white/70">Total Pinjam</span>
	                </div>
                <div class="mt-5">
                    <span class="text-4xl font-black text-white">{{ number_format((int) ($totalPeminjaman ?? 0)) }}</span>
                    <p class="text-xs text-white/75 mt-1">Total buku yang pernah kamu pinjam</p>
                </div>
                <div class="absolute -right-6 -bottom-10 opacity-10 pointer-events-none">
                    <span class="material-symbols-outlined text-[160px] text-white">history_edu</span>
                </div>
            </div>

            <div class="bg-surface-container-low p-6 rounded-xl border-l-4 border-primary">
                <div class="flex justify-between items-start">
                    <span class="material-symbols-outlined text-primary text-3xl">pending_actions</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Sedang Dipinjam</span>
                </div>
                <div class="mt-5">
                    <span class="text-4xl font-black text-primary">{{ number_format((int) ($sedangDipinjam ?? 0)) }}</span>
                    <p class="text-xs text-on-surface-variant mt-1">Buku yang belum kamu kembalikan</p>
                </div>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-surface-container-high">
                        <tr>
                            <th class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest text-outline">Kode</th>
                            <th class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest text-outline">Buku</th>
                            <th class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest text-outline">Tgl Pinjam</th>
                            <th class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest text-outline">Batas</th>
                            <th class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest text-outline">Status</th>
                            <th class="px-8 py-4 text-center text-[10px] font-black uppercase tracking-widest text-outline">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-surface-container-lowest">
                        @forelse ($riwayat as $row)
                            @php
                                $kembali = $row->pengembalian->first();
                                $coverSrc = $row->buku?->cover_image ? asset('storage/'.ltrim(str_replace('public/', '', $row->buku->cover_image), '/')) : null;
                            @endphp
                            <tr class="hover:bg-surface-container-low transition-colors group">
                                <td class="px-8 py-5 text-sm font-mono text-primary font-semibold">{{ $row->kode_peminjaman }}</td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-14 bg-surface-container-highest rounded shadow-sm overflow-hidden flex-shrink-0">
                                            @if ($row->buku?->cover_image)
                                                <img alt="Cover" class="w-full h-full object-cover" src="{{ $coverSrc }}" />
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
                                <td class="px-8 py-5 text-center">
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-surface-container-low text-primary hover:bg-surface-container-high transition-colors"
                                        title="Lihat detail"
                                        aria-label="Lihat detail"
                                        @click="openDetail({
                                            kode: @js($row->kode_peminjaman),
                                            status: @js($row->status),
                                            catatan: @js($row->catatan),
                                            denda: {{ (float) ($row->denda ?? 0) }},
                                            created_at: @js(optional($row->created_at)->toDateString()),
                                            tanggal_pinjam: @js(optional($row->tanggal_pinjam)->toDateString()),
                                            batas_pengembalian: @js(optional($row->batas_pengembalian)->toDateString()),
                                            tanggal_kembali: @js(optional($row->tanggal_kembali)->toDateString()),
                                            buku: {
                                                judul: @js($row->buku?->judul),
                                                penulis: @js($row->buku?->penulis),
                                                kategori: @js($row->buku?->kategori?->nama_kategori),
                                                cover: @js($coverSrc),
                                            },
                                            pengembalian: {
                                                tanggal_kembali_aktual: @js(optional($kembali?->tanggal_kembali_aktual)->toDateString()),
                                                keterlambatan: {{ (int) ($kembali?->keterlambatan ?? 0) }},
                                                denda_otomatis: {{ (float) ($kembali?->denda_otomatis ?? 0) }},
                                                denda_dibayar: {{ (float) ($kembali?->denda_dibayar ?? 0) }},
                                                kondisi_buku: @js($kembali?->kondisi_buku),
                                                catatan: @js($kembali?->catatan),
                                            },
                                        })"
                                    >
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-10 text-sm text-on-surface-variant">Belum ada riwayat peminjaman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-8 py-4 border-t border-outline-variant/10 bg-surface-container-low/30">
                {{ $riwayat->links() }}
            </div>
        </div>

        <!-- Modal Detail Riwayat -->
        <div
            x-show="modalOpen"
            x-transition.opacity.duration.200ms
            class="fixed inset-0 z-[100] flex items-start justify-center px-4 py-6 md:px-12 md:py-12"
            style="display: none;"
            aria-modal="true"
            role="dialog"
        >
            <div class="absolute inset-0 bg-primary/20 backdrop-blur-md" @click="closeDetail()"></div>

            <div class="relative w-full max-w-4xl bg-surface rounded-xl overflow-hidden shadow-ambient flex flex-col" style="max-height: calc(100vh - 3rem);">
                <div class="px-8 py-6 bg-surface-container-low flex items-start justify-between gap-6">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Detail Transaksi</p>
                        <h4 class="text-xl font-extrabold tracking-tight text-primary mt-1" x-text="selected?.kode"></h4>
                    </div>
                    <button type="button" class="p-2 rounded-full hover:bg-surface-container-high transition-colors text-on-surface-variant" @click="closeDetail()">
                        <span class="material-symbols-outlined text-2xl">close</span>
                    </button>
                </div>

                <div class="p-8 space-y-10 flex-1 min-h-0 overflow-y-auto" style="overflow-y: auto;">
                    <!-- Header Buku -->
                    <section class="flex flex-col sm:flex-row items-start gap-6">
                        <div class="bg-surface-container-highest rounded-lg overflow-hidden shadow-sm flex-shrink-0" style="width: 112px; height: 160px;">
                            <template x-if="selected?.buku?.cover">
                                <img alt="Cover" class="w-full h-full object-cover" :src="selected.buku.cover" />
                            </template>
                            <template x-if="!selected?.buku?.cover">
                                <div class="w-full h-full bg-surface-container-high"></div>
                            </template>
                        </div>
                        <div class="flex-1">
                            <div class="inline-block px-2 py-0.5 rounded text-[10px] font-bold tracking-widest uppercase bg-primary-fixed text-on-primary-fixed mb-2" x-text="selected?.buku?.kategori || 'Kategori'"></div>
                            <h3 class="text-lg font-bold text-on-surface leading-tight mb-1" x-text="selected?.buku?.judul || '-'"></h3>
                            <p class="text-on-surface-variant text-sm italic">Oleh <span x-text="selected?.buku?.penulis || '-'"></span></p>
                            <div class="mt-4 flex items-center gap-2">
                                <span class="text-xs font-bold px-3 py-1 rounded-full uppercase tracking-widest" :class="statusClass(selected?.status)" x-text="statusLabel(selected?.status)"></span>
                                <template x-if="selected?.status === 'dikembalikan' && selected?.pengembalian?.kondisi_buku">
                                    <span class="text-xs font-bold px-3 py-1 rounded-full uppercase tracking-widest" :class="kondisiClass(selected?.pengembalian?.kondisi_buku)" x-text="kondisiLabel(selected?.pengembalian?.kondisi_buku)"></span>
                                </template>
                                <template x-if="selected?.status === 'hilang'">
                                    <span class="text-xs font-bold px-3 py-1 rounded-full uppercase tracking-widest bg-error-container text-on-error-container">HILANG</span>
                                </template>
                            </div>
                        </div>
                    </section>

                    <!-- Timeline -->
                    <section>
                        <h4 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-6">Timeline Transaksi</h4>

                        <div class="relative space-y-8 before:absolute before:inset-0 before:ml-5 before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-primary/20 before:via-primary before:to-primary/20">
                            <!-- Request -->
                            <div class="relative flex items-start gap-4">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-primary-container text-on-primary-container shadow shrink-0 z-10">
                                    <span class="material-symbols-outlined text-sm">edit_document</span>
                                </div>
                                <div class="flex-1 p-4 rounded-xl bg-surface-container-low">
                                    <div class="flex items-center justify-between gap-4 mb-1">
                                        <div class="font-bold text-primary">Permintaan Pinjam</div>
                                        <time class="text-xs font-medium text-on-surface-variant uppercase" x-text="fmtDate(selected?.created_at)"></time>
                                    </div>
                                    <div class="text-sm text-on-surface-variant">Siswa mengajukan peminjaman melalui portal.</div>
                                </div>
                            </div>

                            <!-- Approval -->
                            <template x-if="selected?.tanggal_pinjam">
                                <div class="relative flex items-start gap-4">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-primary-container text-on-primary-container shadow shrink-0 z-10">
                                        <span class="material-symbols-outlined text-sm">verified</span>
                                    </div>
                                    <div class="flex-1 p-4 rounded-xl bg-surface-container-low">
                                        <div class="flex items-center justify-between gap-4 mb-1">
                                            <div class="font-bold text-primary">Disetujui Pustakawan</div>
                                            <time class="text-xs font-medium text-on-surface-variant uppercase" x-text="fmtDate(selected?.tanggal_pinjam)"></time>
                                        </div>
                                        <div class="text-sm text-on-surface-variant">Buku telah diserahkan kepada siswa.</div>
                                    </div>
                                </div>
                            </template>

                            <!-- Due Date -->
                            <template x-if="selected?.batas_pengembalian && isReasonableDate(selected?.batas_pengembalian)">
                                <div class="relative flex items-start gap-4">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white"
                                        :class="selected?.status === 'terlambat' ? 'bg-error-container text-on-error-container' : 'bg-primary-container text-on-primary-container'"
                                    >
                                        <span class="material-symbols-outlined text-sm">event_busy</span>
                                    </div>
                                    <div class="flex-1 p-4 rounded-xl"
                                        :class="selected?.status === 'terlambat' ? 'bg-error-container/10 border border-error-container/20' : 'bg-surface-container-low'"
                                    >
                                        <div class="flex items-center justify-between gap-4 mb-1">
                                            <div class="font-bold" :class="selected?.status === 'terlambat' ? 'text-error' : 'text-primary'">Batas Pengembalian</div>
                                            <time class="text-xs font-medium uppercase" :class="selected?.status === 'terlambat' ? 'text-error' : 'text-on-surface-variant'" x-text="fmtDate(selected?.batas_pengembalian)"></time>
                                        </div>
                                        <div class="text-sm" :class="selected?.status === 'terlambat' ? 'text-on-error-container' : 'text-on-surface-variant'">
                                            Masa peminjaman berakhir.
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Return / Lost -->
                            <template x-if="selected?.status === 'dikembalikan' || selected?.status === 'hilang'">
                                <div class="relative flex items-start gap-4">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white"
                                        :class="selected?.status === 'hilang' ? 'bg-error-container text-on-error-container' : 'bg-secondary-fixed text-on-secondary-fixed'"
                                    >
                                        <span class="material-symbols-outlined text-sm" x-text="selected?.status === 'hilang' ? 'report' : 'assignment_turned_in'"></span>
                                    </div>
                                    <div class="flex-1 p-4 rounded-xl bg-surface-container-low">
                                        <div class="flex items-center justify-between gap-4 mb-1">
                                            <div class="font-bold text-primary" x-text="selected?.status === 'hilang' ? 'Dilaporkan Hilang' : 'Dikembalikan'"></div>
                                            <time class="text-xs font-medium text-on-surface-variant uppercase" x-text="fmtDate(selected?.pengembalian?.tanggal_kembali_aktual || selected?.tanggal_kembali)"></time>
                                        </div>
                                        <div class="text-sm text-on-surface-variant" x-text="selected?.status === 'hilang' ? 'Buku dinyatakan hilang saat proses pengembalian.' : 'Pengembalian telah dicatat oleh admin.'"></div>
                                    </div>
                                </div>
                            </template>

                            <!-- Rejected (optional) -->
                            <template x-if="selected?.status === 'ditolak'">
                                <div class="relative flex items-start gap-4">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-surface-container-high text-on-surface-variant shadow shrink-0 z-10">
                                        <span class="material-symbols-outlined text-sm">block</span>
                                    </div>
                                    <div class="flex-1 p-4 rounded-xl bg-surface-container-low">
                                        <div class="flex items-center justify-between gap-4 mb-1">
                                            <div class="font-bold text-primary">Ditolak</div>
                                            <time class="text-xs font-medium text-on-surface-variant uppercase" x-text="fmtDate(selected?.created_at)"></time>
                                        </div>
                                        <div class="text-sm text-on-surface-variant" x-text="selected?.catatan || 'Permintaan ditolak oleh admin.'"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </section>

                    <!-- Fine / Condition -->
                    <template x-if="selected?.status === 'terlambat'">
                        <section class="bg-surface-container-highest/50 rounded-xl p-6 border-l-4 border-error">
                            <div class="flex items-start gap-4">
                                <div class="p-2 bg-error-container text-on-error-container rounded-lg">
                                    <span class="material-symbols-outlined">payments</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-on-surface mb-1">Informasi Denda Keterlambatan</h4>
                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        <div class="bg-surface-container-lowest p-3 rounded-lg shadow-sm">
                                            <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold block mb-1">Durasi Terlambat</span>
                                            <span class="text-xl font-black text-error"><span x-text="overdueDays()"></span> Hari</span>
                                        </div>
                                        <div class="bg-surface-container-lowest p-3 rounded-lg shadow-sm">
                                            <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold block mb-1">Estimasi Denda</span>
                                            <span class="text-xl font-black text-error" x-text="fmtRupiah(overdueDays() * dendaPerHari)"></span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-on-surface-variant mt-4 leading-relaxed">
                                        * Denda dihitung berdasarkan aturan perpustakaan.
                                    </p>
                                </div>
                            </div>
                        </section>
                    </template>

                    <template x-if="selected?.status === 'dikembalikan' && (selected?.pengembalian?.keterlambatan || 0) > 0 && isReasonableDate(selected?.batas_pengembalian) && isReasonableDate(selected?.pengembalian?.tanggal_kembali_aktual || selected?.tanggal_kembali)">
                        <section class="bg-surface-container-highest/50 rounded-xl p-6 border-l-4 border-error">
                            <div class="flex items-start gap-4">
                                <div class="p-2 bg-error-container text-on-error-container rounded-lg">
                                    <span class="material-symbols-outlined">payments</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-on-surface mb-1">Denda Keterlambatan</h4>
                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        <div class="bg-surface-container-lowest p-3 rounded-lg shadow-sm">
                                            <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold block mb-1">Durasi Terlambat</span>
                                            <span class="text-xl font-black text-error"><span x-text="selected?.pengembalian?.keterlambatan || 0"></span> Hari</span>
                                        </div>
                                        <div class="bg-surface-container-lowest p-3 rounded-lg shadow-sm">
                                            <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold block mb-1">Total Denda</span>
                                            <span class="text-xl font-black text-error" x-text="fmtRupiah(selected?.denda || 0)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </template>

                    <template x-if="selected?.status === 'dikembalikan' && selected?.pengembalian?.catatan">
                        <section class="bg-surface-container-low rounded-xl p-6">
                            <h4 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-3">Catatan Pengembalian</h4>
                            <p class="text-sm text-on-surface-variant" x-text="selected?.pengembalian?.catatan"></p>
                        </section>
                    </template>

                    <template x-if="selected?.status === 'hilang' && (selected?.pengembalian?.catatan || selected?.catatan)">
                        <section class="bg-surface-container-low rounded-xl p-6">
                            <h4 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-3">Catatan</h4>
                            <p class="text-sm text-on-surface-variant" x-text="selected?.pengembalian?.catatan || selected?.catatan"></p>
                        </section>
                    </template>
                </div>

                <div class="px-8 py-6 bg-surface-container-low flex items-center justify-end gap-4 border-t border-outline-variant/10">
                    <div class="flex gap-3 w-full sm:w-auto justify-end">
                        <button type="button" class="flex-1 sm:flex-none px-8 py-2.5 bg-primary text-on-primary font-bold rounded-full hover:bg-primary-container transition-all text-sm uppercase tracking-widest" @click="closeDetail()">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
