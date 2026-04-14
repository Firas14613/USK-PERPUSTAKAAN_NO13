<?php

namespace App\Filament\Resources\PengembalianResource\Pages;

use App\Filament\Resources\PengembalianResource;
use Filament\Actions;
use App\Filament\Resources\Pages\CreateRecordRedirectToList;

class CreatePengembalian extends CreateRecordRedirectToList
{
    protected static string $resource = PengembalianResource::class;
}
