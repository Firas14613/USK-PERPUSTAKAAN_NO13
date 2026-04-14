<x-filament::page>
    <div class="space-y-8">
        <div class="flex flex-col gap-4">
            <x-filament::input.wrapper
                prefix-icon="heroicon-m-magnifying-glass"
                inline-prefix
                wire:target="search"
                class="w-full"
            >
                <x-filament::input
                    autocomplete="off"
                    inline-prefix
                    maxlength="1000"
                    placeholder="Search scholastic records..."
                    type="search"
                    wire:model.live.debounce.250ms="search"
                />
            </x-filament::input.wrapper>

            @if ($searchResults['hasQuery'])
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Buku</p>
                        <div class="mt-3 space-y-2">
                            @forelse ($searchResults['buku'] as $item)
                                <a href="{{ $item['url'] }}" class="block rounded-lg px-3 py-2 transition hover:bg-gray-50">
                                    <p class="text-sm font-semibold text-gray-950">{{ $item['title'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $item['subtitle'] }}</p>
                                </a>
                            @empty
                                <p class="text-sm text-gray-500">Tidak ada hasil.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Siswa</p>
                        <div class="mt-3 space-y-2">
                            @forelse ($searchResults['siswa'] as $item)
                                <a href="{{ $item['url'] }}" class="block rounded-lg px-3 py-2 transition hover:bg-gray-50">
                                    <p class="text-sm font-semibold text-gray-950">{{ $item['title'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $item['subtitle'] }}</p>
                                </a>
                            @empty
                                <p class="text-sm text-gray-500">Tidak ada hasil.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Peminjaman</p>
                        <div class="mt-3 space-y-2">
                            @forelse ($searchResults['peminjaman'] as $item)
                                <a href="{{ $item['url'] }}" class="block rounded-lg px-3 py-2 transition hover:bg-gray-50">
                                    <p class="text-sm font-semibold text-gray-950">{{ $item['title'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $item['subtitle'] }}</p>
                                </a>
                            @empty
                                <p class="text-sm text-gray-500">Tidak ada hasil.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div>
            <h2 class="text-3xl font-extrabold tracking-tight text-blue-900">Library Overview</h2>
            <p class="mt-1 text-sm text-gray-500">Skanesa Scholastic Hub real-time performance analytics.</p>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 transition hover:shadow-md">
                <div class="mb-4 flex items-start justify-between">
                    <div class="rounded-xl bg-primary-50 p-3 text-primary-700">
                        <x-filament::icon icon="heroicon-m-book-open" class="h-5 w-5" />
                    </div>
                </div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Total Buku</p>
                <p class="mt-2 text-4xl font-black text-gray-950">{{ number_format($counts['total_buku']) }}</p>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 transition hover:shadow-md">
                <div class="mb-4 flex items-start justify-between">
                    <div class="rounded-xl bg-blue-50 p-3 text-blue-700">
                        <x-filament::icon icon="heroicon-m-users" class="h-5 w-5" />
                    </div>
                </div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Total Siswa Aktif</p>
                <p class="mt-2 text-4xl font-black text-gray-950">{{ number_format($counts['total_siswa_aktif']) }}</p>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 transition hover:shadow-md">
                <div class="mb-4 flex items-start justify-between">
                    <div class="rounded-xl bg-amber-50 p-3 text-amber-700">
                        <x-filament::icon icon="heroicon-m-hand-raised" class="h-5 w-5" />
                    </div>
                    <span class="rounded-full bg-amber-50 px-2 py-1 text-xs font-bold text-amber-700">Attention</span>
                </div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Peminjaman Pending</p>
                <p class="mt-2 text-4xl font-black text-gray-950">{{ number_format($counts['pending']) }}</p>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 transition hover:shadow-md">
                <div class="mb-4 flex items-start justify-between">
                    <div class="rounded-xl bg-red-50 p-3 text-red-700">
                        <x-filament::icon icon="heroicon-m-exclamation-triangle" class="h-5 w-5" />
                    </div>
                    <span class="rounded-full bg-red-50 px-2 py-1 text-xs font-bold text-red-700">Critical</span>
                </div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Peminjaman Terlambat</p>
                <p class="mt-2 text-4xl font-black text-gray-950">{{ number_format($counts['terlambat']) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
            <div class="lg:col-span-8">
                @livewire(\App\Filament\Widgets\PeminjamanPerBulanChart::class)
            </div>

            <div class="lg:col-span-4">
                <div class="rounded-xl bg-primary-600 p-8 text-white shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-bold tracking-tight">Scholastic Alerts</h3>
                            <p class="mt-1 text-sm text-white/80">Ringkasan kondisi sistem & aktivitas terbaru.</p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <div class="rounded-xl bg-white/10 p-4">
                            <p class="text-sm font-semibold">New inventory arrived</p>
                            <p class="mt-1 text-xs text-white/75">
                                {{ $alerts['new_books_7d'] }} buku ditambahkan dalam 7 hari terakhir.
                            </p>
                        </div>
                        <div class="rounded-xl bg-white/10 p-4">
                            <p class="text-sm font-semibold">Pending approvals</p>
                            <p class="mt-1 text-xs text-white/75">
                                {{ $alerts['pending_requests'] }} request menunggu persetujuan admin.
                            </p>
                        </div>
                        <div class="rounded-xl bg-white/10 p-4">
                            <p class="text-sm font-semibold">Overdue loans</p>
                            <p class="mt-1 text-xs text-white/75">
                                {{ $alerts['overdue'] }} peminjaman terlambat perlu tindak lanjut.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a
                            href="{{ \App\Filament\Resources\PeminjamanResource::getUrl('index') }}"
                            class="inline-flex w-full items-center justify-center rounded-full bg-white px-4 py-2.5 text-sm font-bold text-blue-900 transition hover:bg-white/90"
                        >
                            View All Requests
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
            <div class="mb-4 flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold tracking-tight text-gray-950">Recent Requests</h3>
                    <p class="mt-1 text-sm text-gray-500">Daftar request peminjaman yang masih pending.</p>
                </div>
                <a
                    href="{{ \App\Filament\Resources\PeminjamanResource::getUrl('index') }}"
                    class="rounded-full bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-700"
                >
                    Buka Peminjaman
                </a>
            </div>

            {{ $this->table }}
        </div>
    </div>
</x-filament::page>
