<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BukuResource\Pages;
use App\Models\Buku;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BukuResource extends Resource
{
    protected static ?string $model = Buku::class;

    public static function getModelLabel(): string
    {
        return 'Buku';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Buku';
    }

    protected static ?string $slug = 'buku';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Perpustakaan';

    protected static ?string $navigationLabel = 'Buku';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Buku')
                    ->schema([
                        TextInput::make('kode_buku')
                            ->label('Kode Buku')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (?Buku $record) => $record?->peminjaman()->exists() ?? false),
                        TextInput::make('judul')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('penulis')
                            ->label('Penulis')
                            ->required()
                            ->maxLength(255),
                        Select::make('kategori_id')
                            ->label('Kategori')
                            ->relationship('kategori', 'nama_kategori')
                            ->required(),
                        TextInput::make('penerbit')
                            ->label('Penerbit')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('tahun_terbit')
                            ->label('Tahun Terbit')
                            ->required()
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue((int) now()->format('Y') + 1),
                        TextInput::make('isbn')
                            ->label('ISBN')
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(fn ($state) => blank($state) ? null : $state),
                        TextInput::make('jumlah_halaman')
                            ->label('Jumlah Halaman')
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('stok')
                            ->label('Stok')
                            ->required()
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('lokasi_rak')
                            ->label('Lokasi Rak')
                            ->maxLength(50),
                        FileUpload::make('cover_image')
                            ->label('Cover Buku')
                            ->image()
                            ->disk('public')
                            ->directory('covers')
                            ->maxSize(2048)
                            ->panelLayout('integrated')
                            ->imagePreviewHeight(256)
                            ->columnSpanFull(),
                        Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->height(64)
                    ->width(48)
                    ->state(function (Buku $record): ?string {
                        if (blank($record->cover_image)) {
                            return null;
                        }

                        $path = ltrim(str_replace('public/', '', (string) $record->cover_image), '/');

                        return asset('storage/' . $path);
                    })
                    ->extraImgAttributes([
                        'class' => 'rounded-md shadow-sm',
                        'loading' => 'lazy',
                    ]),
                TextColumn::make('judul')
                    ->label('Title & Info')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn (Buku $record): string => Str::of((string) ($record->penerbit ?: ''))->limit(40)),
                TextColumn::make('kode_buku')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->html()
                    ->extraAttributes(['class' => 'whitespace-nowrap'])
                    ->formatStateUsing(fn (?string $state): string => '<span class="font-mono text-xs font-semibold text-primary-700">'.e((string) $state).'</span>'),
                TextColumn::make('penulis')
                    ->label('Author')
                    ->searchable(),
                TextColumn::make('kategori.nama_kategori')
                    ->sortable()
                    ->label('Category')
                    ->formatStateUsing(function (?string $state): string {
                        $name = Str::of((string) $state)->lower()->trim()->toString();
                        $variant = match (true) {
                            str_contains($name, 'teknik') => 'secondary',
                            str_contains($name, 'sastra') => 'tertiary',
                            default => 'neutral',
                        };

                        return '<span class="ia-badge ia-badge--'.$variant.'">'.e($state ?: '-').'</span>';
                    })
                    ->html(),
                TextColumn::make('stok')
                    ->label('Stock')
                    ->sortable()
                    ->formatStateUsing(function ($state): string {
                        $stock = (int) $state;
                        $dot = match (true) {
                            $stock <= 0 => 'error',
                            $stock <= 3 => 'warning',
                            default => 'success',
                        };

                        return '<div class="ia-stock"><span class="ia-stock-dot ia-stock-dot--'.$dot.'"></span><span class="ia-stock-val">'.e((string) $stock).'</span></div>';
                    })
                    ->html(),
            ])
            ->filters([
                SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama_kategori'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Hapus')
                    ->visible(fn (Buku $record) => ! $record->peminjaman()
                        ->whereIn('status', ['pending', 'dipinjam', 'terlambat'])
                        ->exists()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListBukus::route('/'),
            'create' => Pages\CreateBuku::route('/create'),
            'edit' => Pages\EditBuku::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
