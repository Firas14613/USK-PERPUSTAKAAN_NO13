<x-filament-panels::page
    @class([
        'fi-resource-create-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'ia-create-buku',
    ])
>
    <x-filament-panels::form
        id="form"
        :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
        wire:submit="create"
    >
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 md:p-8">
            <div class="mb-4 text-xs font-semibold text-gray-500">
                <a href="{{ route('filament.admin.pages.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
                <span class="px-1">/</span>
                <a href="{{ \App\Filament\Resources\BukuResource::getUrl('index') }}" class="hover:text-gray-700">Buku</a>
                <span class="px-1">/</span>
                <span class="text-gray-700">Create</span>
            </div>

            <div class="mb-6 flex items-start justify-between gap-6">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-blue-900">Tambah Koleksi Baru</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Lengkapi informasi katalog buku secara detail untuk sistem manajemen perpustakaan.
                    </p>
                </div>
            </div>

            {{ $this->form }}

            <div class="mt-8 border-t border-gray-950/5 pt-6">
                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </div>
        </div>
    </x-filament-panels::form>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl bg-blue-50 p-6 ring-1 ring-gray-950/5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-bold text-blue-900">Verifikasi Otomatis</p>
                    <p class="mt-1 text-xs text-blue-900/70">
                        Sistem akan melakukan pengecekan duplikasi ISBN secara real-time.
                    </p>
                </div>
                <div class="rounded-xl bg-white/70 p-3 text-blue-900">
                    <x-filament::icon icon="heroicon-m-shield-check" class="h-5 w-5" />
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-amber-50 p-6 ring-1 ring-gray-950/5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-bold text-amber-900">Visibilitas Katalog</p>
                    <p class="mt-1 text-xs text-amber-900/70">
                        Buku yang disimpan akan langsung tersedia di aplikasi mobile siswa.
                    </p>
                </div>
                <div class="rounded-xl bg-white/70 p-3 text-amber-900">
                    <x-filament::icon icon="heroicon-m-eye" class="h-5 w-5" />
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-gray-100 p-6 ring-1 ring-gray-950/5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-bold text-gray-900">Log Perubahan</p>
                    <p class="mt-1 text-xs text-gray-700">
                        Setiap entri akan dicatat dalam riwayat aktivitas admin.
                    </p>
                </div>
                <div class="rounded-xl bg-white/70 p-3 text-gray-900">
                    <x-filament::icon icon="heroicon-m-clock" class="h-5 w-5" />
                </div>
            </div>
        </div>
    </div>

    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>
