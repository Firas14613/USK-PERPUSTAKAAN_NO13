<x-filament::page>
    {{-- Header Section --}}
    <div class="mb-8">
        <h2 class="text-3xl font-black text-gray-900 tracking-tight">Laporan Sirkulasi</h2>
        <p class="text-gray-500 mt-1">Rekapitulasi data peminjaman dan pengembalian koleksi perpustakaan.</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                    </svg>
                </div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Total Pinjaman</p>
                <h3 class="text-3xl font-black text-gray-900 mt-1">{{ number_format($totalPinjaman) }}</h3>
            </div>
            <div class="mt-4 flex items-center gap-1 text-xs font-medium text-emerald-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <span>Semua waktu</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Total Denda</p>
                <h3 class="text-3xl font-black text-gray-900 mt-1">Rp {{ number_format($totalDenda, 0, ',', '.') }}</h3>
            </div>
            <div class="mt-4 flex items-center gap-1 text-xs font-medium text-amber-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $activeDenda }} tagihan aktif</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Terlambat</p>
                <h3 class="text-3xl font-black text-gray-900 mt-1">{{ number_format($totalTerlambat) }}</h3>
            </div>
            <div class="mt-4 flex items-center gap-1 text-xs font-medium text-red-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Perlu tindak lanjut</span>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{ $this->table }}
    </div>
</x-filament::page>
