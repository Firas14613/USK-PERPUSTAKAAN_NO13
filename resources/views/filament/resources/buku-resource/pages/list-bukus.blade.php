@php
    use Filament\Support\Facades\FilamentView;
@endphp

<x-filament-panels::page
    @class([
        'fi-resource-list-records-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
    ])
>
    <div class="flex flex-col gap-y-6">
        <x-filament::input.wrapper
            prefix-icon="heroicon-m-magnifying-glass"
            inline-prefix
            wire:target="tableSearch"
            class="w-full"
        >
            <x-filament::input
                autocomplete="off"
                inline-prefix
                maxlength="1000"
                placeholder="Quick search catalog..."
                type="search"
                wire:model.live.debounce.250ms="tableSearch"
            />
        </x-filament::input.wrapper>

        <x-filament-panels::resources.tabs />

        {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE, scopes: $this->getRenderHookScopes()) }}

        {{ $this->table }}

        {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER, scopes: $this->getRenderHookScopes()) }}

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Total Catalog Volume</p>
                        <p class="mt-2 text-3xl font-black text-gray-950">{{ number_format($stats['total_catalog']) }}</p>
                    </div>
                    <div class="rounded-xl bg-primary-50 p-3 text-primary-700">
                        <x-filament::icon icon="heroicon-m-book-open" class="h-5 w-5" />
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Currently Borrowed</p>
                        <p class="mt-2 text-3xl font-black text-gray-950">{{ number_format($stats['currently_borrowed']) }}</p>
                    </div>
                    <div class="rounded-xl bg-blue-50 p-3 text-blue-700">
                        <x-filament::icon icon="heroicon-m-arrow-path" class="h-5 w-5" />
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Overdue Returns</p>
                        <p class="mt-2 text-3xl font-black text-gray-950">{{ number_format($stats['overdue']) }}</p>
                    </div>
                    <div class="rounded-xl bg-red-50 p-3 text-red-700">
                        <x-filament::icon icon="heroicon-m-exclamation-circle" class="h-5 w-5" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

