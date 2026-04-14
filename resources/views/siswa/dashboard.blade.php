@extends('layouts.siswa')

@section('title', 'Dashboard')
@section('heading', 'Selamat datang, '.(auth()->user()->nama_lengkap ?? auth()->user()->username).'!')

	@section('content')
	    <div class="mt-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
	        <a href="{{ route('siswa.koleksi') }}" class="group bg-surface-container-lowest rounded-xl p-8 transition-all duration-300 hover:shadow-ambient relative overflow-hidden">
	            <div class="absolute inset-0 bg-gradient-to-tr from-primary/10 to-transparent"></div>
	            <div class="relative">
                <span class="material-symbols-outlined text-primary mb-6 text-4xl">book_5</span>
                <h3 class="text-2xl font-bold text-primary mb-2">Koleksi Buku</h3>
                <p class="text-on-surface-variant text-sm mb-6">Jelajahi katalog buku dan buat request peminjaman.</p>
                <span class="text-sm font-bold text-primary group-hover:translate-x-1 inline-flex items-center gap-2 transition-transform">
                    Buka <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </span>
	            </div>
	        </a>

	        <a href="{{ route('siswa.riwayat') }}" class="group bg-surface-container-lowest rounded-xl p-8 transition-all duration-300 hover:shadow-ambient relative overflow-hidden">
	            <div class="absolute inset-0 bg-gradient-to-tr from-secondary/10 to-transparent"></div>
	            <div class="relative">
                <span class="material-symbols-outlined text-secondary mb-6 text-4xl">history</span>
                <h3 class="text-2xl font-bold text-secondary mb-2">Riwayat Pinjam</h3>
                <p class="text-on-surface-variant text-sm mb-6">Lihat status request, dipinjam, hingga pengembalian.</p>
                <span class="text-sm font-bold text-secondary group-hover:translate-x-1 inline-flex items-center gap-2 transition-transform">
                    Lihat <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </span>
	            </div>
	        </a>

	        <a href="{{ route('siswa.profil.edit') }}" class="group bg-surface-container-lowest rounded-xl p-8 transition-all duration-300 hover:shadow-ambient relative overflow-hidden">
	            <div class="absolute inset-0 bg-gradient-to-tr from-tertiary/10 to-transparent"></div>
	            <div class="relative">
                <span class="material-symbols-outlined text-tertiary mb-6 text-4xl">account_circle</span>
                <h3 class="text-2xl font-bold text-tertiary mb-2">Profil Saya</h3>
                <p class="text-on-surface-variant text-sm mb-6">Perbarui data akun dan keamanan password.</p>
                <span class="text-sm font-bold text-tertiary group-hover:translate-x-1 inline-flex items-center gap-2 transition-transform">
                    Edit <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </span>
	            </div>
	        </a>

	        @if ((float) ($totalDenda ?? 0) > 0)
	            <div class="lg:col-span-3">
	                <div class="relative overflow-hidden rounded-xl p-7 bg-error-container text-on-error-container border border-error/15 shadow-sm">
	                    <div class="absolute inset-0 bg-gradient-to-r from-error/20 to-transparent pointer-events-none"></div>
	                    <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
	                        <div class="flex items-start gap-4">
	                            <div class="h-12 w-12 rounded-2xl bg-error/15 flex items-center justify-center flex-shrink-0">
	                                <span class="material-symbols-outlined text-error text-2xl">receipt_long</span>
	                            </div>
	                            <div>
	                                <p class="text-[10px] font-black uppercase tracking-widest text-error/80">Total Denda</p>
	                                <p class="text-sm text-on-error-container/80 mt-1">Akumulasi denda yang tercatat dari transaksi peminjaman.</p>
	                            </div>
	                        </div>
	                        <div class="text-left sm:text-right">
	                            <p class="text-3xl sm:text-4xl font-black text-error">
	                                Rp {{ number_format((float) ($totalDenda ?? 0), 0, ',', '.') }}
	                            </p>
	                            <p class="text-xs text-on-error-container/70 mt-1">Semakin kecil semakin baik.</p>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        @endif
	    </div>

	    <section class="mt-16">
	        <h4 class="text-xl font-bold text-primary mb-8 flex items-center gap-2">
	            <span class="w-8 h-[2px] bg-primary"></span>
            Buku Sedang Dipinjam
        </h4>

        <div class="space-y-4">
            @forelse ($sedangDipinjam as $p)
                @php
                    $batas = $p->batas_pengembalian;
                    $hariSisa = $batas ? now()->startOfDay()->diffInDays($batas->startOfDay(), false) : null;
                    $isTerlambat = $hariSisa !== null && $hariSisa < 0;
                @endphp
                <div class="flex items-center gap-6 p-4 bg-surface-container-low rounded-xl hover:bg-surface-container-high transition-colors {{ $isTerlambat ? 'border border-error/10' : '' }}">
                    <div class="w-16 h-24 bg-surface-container-highest rounded shadow-sm overflow-hidden flex-shrink-0">
                        @if ($p->buku?->cover_image)
                            <img alt="Cover" class="w-full h-full object-cover" src="{{ asset('storage/'.ltrim(str_replace('public/', '', $p->buku->cover_image), '/')) }}" />
                        @else
                            <div class="w-full h-full bg-surface-container-lowest"></div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <span class="px-2 py-1 bg-secondary-fixed text-on-secondary-fixed text-[10px] font-bold rounded-full uppercase tracking-tighter">
                            {{ $p->buku?->kategori?->nama_kategori ?? 'Kategori' }}
                        </span>
                        <h5 class="text-lg font-bold text-on-surface mt-1">{{ $p->buku?->judul }}</h5>
                        <p class="text-xs text-on-surface-variant">
                            Oleh {{ $p->buku?->penulis ?? '-' }} • Dipinjam: {{ optional($p->tanggal_pinjam)->format('d M Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold uppercase {{ $isTerlambat ? 'text-error' : 'text-on-surface-variant' }} mb-1">
                            {{ $isTerlambat ? 'TERLAMBAT' : 'Tenggat Waktu' }}
                        </p>
                        <p class="text-sm font-bold {{ $isTerlambat ? 'text-error' : 'text-primary' }}">
                            {{ $batas ? $batas->format('d M Y') : '—' }}
                        </p>
                        @if ($hariSisa !== null)
                            <span class="text-[10px] {{ $isTerlambat ? 'text-error font-medium' : 'text-on-surface-variant' }}">
                                {{ $isTerlambat ? 'Lewat '.abs($hariSisa).' Hari' : 'Sisa '.$hariSisa.' Hari' }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-surface-container-low rounded-xl p-8">
                    <p class="text-sm text-on-surface-variant">Belum ada buku yang sedang dipinjam.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
