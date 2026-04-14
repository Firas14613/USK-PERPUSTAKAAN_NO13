<?php

namespace App\Filament\Resources\KategoriBukuResource\Pages;

use App\Filament\Resources\KategoriBukuResource;
use Filament\Actions;
use App\Filament\Resources\Pages\EditRecordRedirectToList;

class EditKategoriBuku extends EditRecordRedirectToList
{
    protected static string $resource = KategoriBukuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
