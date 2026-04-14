@extends('layouts.siswa')

@section('title', 'Koleksi Buku')
@section('heading', 'Koleksi Buku')

@section('content')
    <section class="mt-10" x-data="{
        modalOpen: false,
        selected: null,
        searchQuery: @js($search ?? ''),
        kategoriValue: @js($kategoriId ? (string) $kategoriId : ''),
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
        statusLabel(payload) {
            if (payload.sudahAktif) return 'Sudah Request/Aktif';
            if (payload.stok < 1) return 'Stok Habis';
            return 'Tersedia';
        },
        statusClasses(payload) {
            if (payload.sudahAktif) return 'bg-tertiary-fixed text-on-tertiary-fixed';
            if (payload.stok < 1) return 'bg-error-container text-on-error-container';
            return 'bg-secondary-fixed text-on-secondary-fixed';
        },
        applyFilters() {
            const params = new URLSearchParams(window.location.search);

            const q = (this.searchQuery || '').trim();
            if (q) params.set('q', q); else params.delete('q');

            const kategori = (this.kategoriValue || '').trim();
            if (kategori) params.set('kategori_id', kategori); else params.delete('kategori_id');

            params.delete('page');

            const qs = params.toString();
            window.location.assign(window.location.pathname + (qs ? ('?' + qs) : ''));
        },
        clearSearch() {
            this.searchQuery = '';
            this.applyFilters();
        },
    }" @keydown.escape.window="closeDetail()">
	        <div class="max-w-7xl mx-auto">
	            <form method="GET" class="flex flex-col gap-6 lg:flex-row lg:items-end">
	                <div class="max-w-2xl">
	                    <div>
	                        <label class="block text-[10px] font-bold uppercase tracking-widest text-outline mb-2">Cari</label>
	                        <div class="relative">
	                            <input
	                                type="text"
                                name="q"
                                x-model="searchQuery"
                                @input.debounce.350ms="applyFilters()"
                                value="{{ $search ?? '' }}"
                                placeholder="Cari judul buku atau pengarang..."
                                class="w-full h-12 rounded-xl border-outline-variant/20 bg-white/60 px-4 pr-20 focus:border-primary focus:ring-primary/20"
                                autocomplete="off"
                            />

                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-outline flex items-center gap-2">
                                <button
                                    type="button"
                                    class="rounded-full p-1 hover:bg-surface-container-high transition-colors"
                                    x-show="(searchQuery || '').trim().length"
                                    x-transition.opacity.duration.150ms
                                    @click="clearSearch()"
                                    aria-label="Hapus pencarian"
                                    title="Hapus pencarian"
                                >
                                    <span class="material-symbols-outlined text-[18px] leading-none">close</span>
                                </button>
                                <span class="material-symbols-outlined">search</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-panel rounded-xl p-4 flex flex-col sm:flex-row sm:items-end gap-4 lg:ms-auto">
                    <div class="min-w-[220px]">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-outline mb-2">Kategori</label>
                        <select
                            name="kategori_id"
                            x-model="kategoriValue"
                            @change="applyFilters()"
                            class="w-full h-12 rounded-xl border-outline-variant/20 bg-white/60 px-4 focus:border-primary focus:ring-primary/20"
                        >
                            <option value="">Semua</option>
                            @foreach ($kategori as $k)
                                <option value="{{ $k->id }}" @selected((string) $kategoriId === (string) $k->id)>{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="editorial-gradient w-full sm:w-auto h-12 text-white font-bold px-6 rounded-xl transition-all active:scale-95">
                        Filter
                    </button>
                </div>
            </form>

            <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach ($buku as $item)
                    @php
                        $sudahAktif = $aktifByBukuId->has($item->id);
                        $habis = (int) $item->stok < 1;
                        $readOnly = (auth()->user()->siswa?->status ?? 'aktif') === 'lulus';
                        $disabled = $habis || $sudahAktif || $readOnly;
                        $coverSrc = $item->cover_image ? asset('storage/'.ltrim(str_replace('public/', '', $item->cover_image), '/')) : null;
                    @endphp
                    <div
                        role="button"
                        tabindex="0"
                        class="group bg-surface-container-lowest rounded-xl p-4 transition-all duration-300 hover:shadow-ambient flex flex-col h-full cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary/20"
                        @click="openDetail({
                            id: {{ $item->id }},
                            judul: @js($item->judul),
                            penulis: @js($item->penulis),
                            kategori: @js($item->kategori?->nama_kategori ?? 'Kategori'),
                            isbn: @js($item->isbn),
                            penerbit: @js($item->penerbit),
                            tahun_terbit: @js($item->tahun_terbit),
                            jumlah_halaman: @js($item->jumlah_halaman),
                            lokasi_rak: @js($item->lokasi_rak),
                            deskripsi: @js(trim(strip_tags($item->deskripsi ?? ''))),
                            stok: {{ (int) $item->stok }},
                            sudahAktif: {{ $sudahAktif ? 'true' : 'false' }},
                            disabled: {{ $disabled ? 'true' : 'false' }},
                            disabledLabel: @js($habis ? 'STOK HABIS' : ($sudahAktif ? 'SUDAH REQUEST/AKTIF' : 'PINJAM')),
                            coverSrc: @js($coverSrc),
                        })"
                        @keydown.enter.prevent="$el.click()"
                        @keydown.space.prevent="$el.click()"
                    >
                        <div class="relative aspect-[3/4] rounded-lg overflow-hidden mb-5 bg-surface-container-low">
                            @if ($item->cover_image)
                                <img alt="Cover" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="{{ $coverSrc }}" />
                            @else
                                <div class="w-full h-full bg-surface-container-lowest"></div>
                            @endif
                            <div class="absolute top-3 right-3">
                                <span class="bg-secondary-fixed text-on-secondary-fixed text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest shadow-sm">
                                    Stok: {{ $item->stok }}
                                </span>
                            </div>
                        </div>

                        <div class="flex-1 space-y-1">
                            <p class="text-[10px] font-bold text-on-tertiary-fixed-variant tracking-widest uppercase">{{ $item->kategori?->nama_kategori ?? 'Kategori' }}</p>
                            <h3 class="text-lg font-bold text-on-surface leading-tight">{{ $item->judul }}</h3>
                            <p class="text-sm text-on-surface-variant font-medium">{{ $item->penulis }}</p>
                        </div>

                        <div class="mt-6">
                            <form method="POST" action="{{ route('siswa.pinjam') }}" @click.stop>
                                @csrf
                                <input type="hidden" name="buku_id" value="{{ $item->id }}" />
                                <button
                                    type="submit"
                                    @click.stop
                                    @disabled($disabled)
                                    class="w-full py-3 rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2 font-bold {{ $disabled ? 'bg-surface-container-low text-outline cursor-not-allowed' : 'bg-primary hover:bg-primary-container text-white' }}"
                                >
                                    <span class="material-symbols-outlined text-sm">bookmark_add</span>
                                    {{ $readOnly ? 'READ-ONLY (LULUS)' : ($habis ? 'STOK HABIS' : ($sudahAktif ? 'SUDAH REQUEST/AKTIF' : 'PINJAM')) }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-16">
                {{ $buku->links() }}
            </div>
        </div>

        <!-- Modal Detail Buku -->
        <div
            x-show="modalOpen"
            x-transition.opacity.duration.200ms
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-12"
            style="display: none;"
            aria-modal="true"
            role="dialog"
        >
            <div class="absolute inset-0 bg-primary/20 backdrop-blur-md" @click="closeDetail()"></div>

	            <div
	                class="relative w-full max-w-5xl bg-surface rounded-xl overflow-hidden shadow-[0_20px_40px_rgba(0,35,111,0.12)] flex flex-col md:flex-row animate-in fade-in zoom-in duration-200 max-h-[calc(100vh-2rem)] md:h-[min(740px,calc(100vh-6rem))]"
	                @click.stop
	            >
	                <div class="w-full md:w-[40%] bg-surface-container-low p-8 flex flex-col items-center justify-center relative overflow-hidden">
	                    <div class="absolute top-0 left-0 w-full h-full opacity-5 pointer-events-none bg-[radial-gradient(circle_at_top_right,_#00236f_0%,_transparent_70%)]"></div>

                    <div class="relative z-10 w-full max-w-[280px]">
                        <div class="aspect-[2/3] rounded-lg shadow-2xl overflow-hidden">
                            <template x-if="selected?.coverSrc">
                                <img alt="Book Cover" class="w-full h-full object-cover" :src="selected.coverSrc" />
                            </template>
                            <template x-if="!selected?.coverSrc">
                                <div class="w-full h-full bg-surface-container-high"></div>
                            </template>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-4 z-10">
                        <div class="text-center px-4">
                            <div class="text-xs font-bold tracking-widest text-on-surface-variant uppercase mb-1">Status</div>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold" :class="statusClasses(selected)">
                                <span class="w-2 h-2 rounded-full bg-primary" :class="selected?.stok < 1 ? 'opacity-60' : 'animate-pulse'"></span>
                                <span x-text="statusLabel(selected)"></span>
                            </div>
                        </div>
                        <div class="w-px h-full bg-outline-variant/30"></div>
                        <div class="text-center px-4">
                            <div class="text-xs font-bold tracking-widest text-on-surface-variant uppercase mb-1">Stok</div>
                            <div class="text-sm font-bold text-primary"><span x-text="selected?.stok ?? 0"></span> buku</div>
                        </div>
                    </div>
	                </div>

	                <div class="w-full md:w-[60%] bg-surface-container-lowest p-8 md:p-12 flex flex-col relative min-h-0">
	                    <button type="button" class="absolute top-6 right-6 p-2 rounded-full hover:bg-surface-container-high transition-colors text-on-surface-variant" @click="closeDetail()">
	                        <span class="material-symbols-outlined text-2xl">close</span>
	                    </button>

                    <div class="mb-8">
                        <div class="inline-block px-3 py-1 bg-primary-fixed text-on-primary-fixed text-[10px] font-black tracking-widest uppercase rounded-full mb-3" x-text="selected?.kategori"></div>
                        <h2 class="text-3xl font-extrabold tracking-tight text-primary leading-tight mb-2" x-text="selected?.judul"></h2>
                        <p class="text-lg text-on-surface-variant font-medium">
                            Oleh <span class="text-primary underline decoration-primary/20 underline-offset-4 decoration-2" x-text="selected?.penulis"></span>
                        </p>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8 py-6 border-y border-outline-variant/10">
                        <div>
                            <div class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-1">ISBN</div>
                            <div class="text-sm font-semibold text-on-surface" x-text="selected?.isbn || '-'"></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-1">Penerbit</div>
                            <div class="text-sm font-semibold text-on-surface" x-text="selected?.penerbit || '-'"></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-1">Tahun</div>
                            <div class="text-sm font-semibold text-on-surface" x-text="selected?.tahun_terbit || '-'"></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-1">Halaman</div>
                            <div class="text-sm font-semibold text-on-surface" x-text="selected?.jumlah_halaman || '-'"></div>
	                        </div>
	                    </div>

	                    <div class="flex-1 min-h-0 mb-8">
	                        <div class="h-full overflow-y-auto pr-2">
	                            <h3 class="text-xs font-black uppercase tracking-widest text-on-surface mb-3">Sinopsis</h3>
	                            <p class="text-on-surface-variant leading-relaxed text-sm italic whitespace-pre-line" x-text="selected?.deskripsi || 'Belum ada sinopsis untuk buku ini.'"></p>

	                            <template x-if="selected?.lokasi_rak">
	                                <div class="mt-6">
	                                    <div class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-1">Lokasi Rak</div>
	                                    <div class="text-sm font-semibold text-on-surface" x-text="selected?.lokasi_rak"></div>
	                                </div>
	                            </template>
	                        </div>
	                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <form method="POST" action="{{ route('siswa.pinjam') }}" class="flex-1">
                            @csrf
                            <input type="hidden" name="buku_id" :value="selected?.id" />
                            <button
                                type="submit"
                                :disabled="selected?.disabled"
                                class="w-full py-4 px-6 rounded-lg font-bold tracking-tight text-sm transition-all flex items-center justify-center gap-2"
                                :class="selected?.disabled ? 'bg-surface-container-high text-outline cursor-not-allowed' : 'bg-primary text-on-primary hover:opacity-95 active:scale-[0.98] shadow-lg shadow-primary/20'"
                            >
                                <span class="material-symbols-outlined">auto_stories</span>
                                <span x-text="selected?.disabled ? selected.disabledLabel : 'PINJAM SEKARANG'"></span>
                            </button>
                        </form>

                        <button type="button" class="p-4 rounded-lg bg-surface-container-high text-primary hover:bg-secondary-container transition-colors" @click="closeDetail()">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
