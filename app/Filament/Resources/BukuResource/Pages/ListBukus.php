<?php

namespace App\Filament\Resources\BukuResource\Pages;

use App\Filament\Resources\BukuResource;
use App\Models\Buku;
use App\Models\Peminjaman;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBukus extends ListRecords
{
    protected static string $resource = BukuResource::class;

    protected static string $view = 'filament.resources.buku-resource.pages.list-bukus';

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(Buku::query()->count()),
            'tersedia' => Tab::make('Tersedia')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('stok', '>', 0))
                ->badge(Buku::query()->where('stok', '>', 0)->count())
                ->badgeColor('success'),
            'dipinjam' => Tab::make('Dipinjam')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('peminjaman', fn (Builder $peminjamanQuery) => $peminjamanQuery->whereIn('status', ['dipinjam', 'terlambat'])))
                ->badge(Buku::query()->whereHas('peminjaman', fn (Builder $peminjamanQuery) => $peminjamanQuery->whereIn('status', ['dipinjam', 'terlambat']))->count())
                ->badgeColor('warning'),
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Manage and curate your scholastic library collections.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Book')
                ->icon('heroicon-m-plus'),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'stats' => [
                'total_catalog' => Buku::query()->count(),
                'currently_borrowed' => Peminjaman::query()->whereIn('status', ['dipinjam', 'terlambat'])->count(),
                'overdue' => Peminjaman::query()->where('status', 'terlambat')->count(),
            ],
        ];
    }
}
