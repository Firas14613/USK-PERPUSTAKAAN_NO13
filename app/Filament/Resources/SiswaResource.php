<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Models\Siswa;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $slug = 'siswa';

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Perpustakaan';

    protected static ?string $navigationLabel = 'Siswa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Akun')
                    ->schema([
                        TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->maxLength(255)
                            ->rule(fn (?Siswa $record) => Rule::unique('users', 'username')->ignore($record?->user_id)),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->rule(fn (?Siswa $record) => Rule::unique('users', 'email')->ignore($record?->user_id)),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn (?Siswa $record) => $record === null)
                            ->dehydrated(fn ($state) => filled($state)),
                        TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('telepon')
                            ->label('Telepon')
                            ->maxLength(20),
                    ])
                    ->columns(2),

                Section::make('Data Siswa')
                    ->schema([
                        TextInput::make('nis')
                            ->label('NIS')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        TextInput::make('kelas')
                            ->label('Kelas')
                            ->required()
                            ->maxLength(20),
                        TextInput::make('jurusan')
                            ->label('Jurusan')
                            ->maxLength(100),
                        DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'aktif' => 'Aktif',
                                'lulus' => 'Lulus',
                                'keluar' => 'Keluar',
                            ])
                            ->default('aktif')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.nama_lengkap')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kelas')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jurusan')
                    ->label('Jurusan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'aktif',
                        'gray' => 'lulus',
                        'danger' => 'keluar',
                    ])
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'lulus' => 'Lulus',
                        'keluar' => 'Keluar',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}
