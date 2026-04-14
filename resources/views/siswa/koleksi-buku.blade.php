@extends('layouts.siswa')

@section('title', 'Koleksi Buku')
@section('heading', 'Koleksi Buku')

@section('content')
    <section class="mt-10">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-outline">Arsip Kurasi</p>
                <h3 class="text-4xl font-black tracking-tight text-primary">Koleksi Buku</h3>
                <p class="mt-3 text-on-surface-variant max-w-2xl">Pilih kategori, lihat ketersediaan, lalu buat request peminjaman.</p>
            </div>

            <form method="GET" class="glass-panel rounded-xl p-4 flex items-center gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-outline mb-2">Kategori</label>
                    <select name="kategori_id" class="rounded-xl border-outline-variant/20 bg-white/60 focus:border-primary focus:ring-primary/20">
                        <option value="">Semua</option>
                        @foreach ($kategori as $k)
                            <option value="{{ $k->id }}" @selected((string) $kategoriId === (string) $k->id)>{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="editorial-gradient text-white font-bold px-6 py-3 rounded-xl transition-all active:scale-95">
                    Filter
                </button>
            </form>
        </div>

        <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach ($buku as $item)
                @php
                    $sudahAktif = $aktifByBukuId->has($item->id);
                    $habis = (int) $item->stok < 1;
                    $disabled = $habis || $sudahAktif;
                @endphp
                <div class="group bg-surface-container-lowest rounded-xl p-4 transition-all duration-300 hover:shadow-ambient flex flex-col h-full">
                    <div class="relative aspect-[3/4] rounded-lg overflow-hidden mb-5 bg-surface-container-low">
                        @if ($item->cover_image)
                            <img alt="Cover" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="{{ asset('storage/'.ltrim(str_replace('public/', '', $item->cover_image), '/')) }}" />
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
                        <form method="POST" action="{{ route('siswa.pinjam') }}">
                            @csrf
                            <input type="hidden" name="buku_id" value="{{ $item->id }}" />
                            <button
                                type="submit"
                                @disabled($disabled)
                                class="w-full py-3 rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2 font-bold {{ $disabled ? 'bg-surface-container-low text-outline cursor-not-allowed' : 'bg-primary hover:bg-primary-container text-white' }}"
                            >
                                <span class="material-symbols-outlined text-sm">bookmark_add</span>
                                {{ $habis ? 'STOK HABIS' : ($sudahAktif ? 'SUDAH REQUEST/AKTIF' : 'PINJAM') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-16">
            {{ $buku->links() }}
        </div>
    </section>
@endsection

