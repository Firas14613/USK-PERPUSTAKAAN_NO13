<?php

namespace App\Filament\Resources\KategoriBukuResource\Pages;

use App\Filament\Resources\KategoriBukuResource;
use Filament\Actions;
use App\Filament\Resources\Pages\CreateRecordRedirectToList;

class CreateKategoriBuku extends CreateRecordRedirectToList
{
    protected static string $resource = KategoriBukuResource::class;
}
