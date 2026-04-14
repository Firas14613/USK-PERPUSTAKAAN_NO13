<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriBukuResource\Pages;
use App\Models\KategoriBuku;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class KategoriBukuResource extends Resource
{
    protected static ?string $model = KategoriBuku::class;

    protected static ?string $slug = 'kategori-buku';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Perpustakaan';

    protected static ?string $navigationLabel = 'Kategori Buku';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Kategori')
                    ->schema([
                        TextInput::make('nama_kategori')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(100),
                        Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(4),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kategori')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('has_buku')
                    ->label('Punya Buku')
                    ->query(fn ($query) => $query->whereHas('buku')),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKategoriBukus::route('/'),
            'create' => Pages\CreateKategoriBuku::route('/create'),
            'edit' => Pages\EditKategoriBuku::route('/{record}/edit'),
        ];
    }
}
