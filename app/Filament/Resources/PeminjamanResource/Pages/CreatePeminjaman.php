<?php

namespace App\Filament\Resources\PeminjamanResource\Pages;

use App\Filament\Resources\PeminjamanResource;
use Filament\Actions;
use App\Filament\Resources\Pages\CreateRecordRedirectToList;

class CreatePeminjaman extends CreateRecordRedirectToList
{
    protected static string $resource = PeminjamanResource::class;
}
