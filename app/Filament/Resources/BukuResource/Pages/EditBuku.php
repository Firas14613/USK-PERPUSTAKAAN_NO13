<?php

namespace App\Filament\Resources\BukuResource\Pages;

use App\Filament\Resources\BukuResource;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use App\Filament\Resources\Pages\EditRecordRedirectToList;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Support\Htmlable;

class EditBuku extends EditRecordRedirectToList
{
    protected static string $resource = BukuResource::class;

    protected static bool $canCreateAnother = false;

    public static string|Alignment $formActionsAlignment = Alignment::End;

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    Section::make('Cover Buku')
                        ->schema([
                            FileUpload::make('cover_image')
                                ->label('Unggah Sampul Buku')
                                ->image()
                                ->disk('public')
                                ->directory('covers')
                                ->maxSize(2048)
                                ->imagePreviewHeight(256)
                                ->panelLayout('integrated')
                                ->helperText('Format JPG, PNG atau WebP. Rasio disarankan 3:4.'),
                            ViewField::make('catalog_hint')
                                ->view('filament.forms.buku.catalog-hint')
                                ->dehydrated(false),
                        ])
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 1,
                        ]),

                    Section::make('Detail Koleksi')
                        ->schema([
                            TextInput::make('kode_buku')
                                ->label('Kode Buku')
                                ->required()
                                ->maxLength(50)
                                ->unique(ignoreRecord: true)
                                ->placeholder('SKN-2024-001'),

                            TextInput::make('isbn')
                                ->label('ISBN')
                                ->maxLength(20)
                                ->unique(ignoreRecord: true)
                                ->dehydrateStateUsing(fn ($state) => blank($state) ? null : $state)
                                ->placeholder('978-602-...'),

                            TextInput::make('judul')
                                ->label('Judul Buku')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Contoh: Dasar-Dasar Teknik Elektronika')
                                ->columnSpanFull(),

                            TextInput::make('penulis')
                                ->label('Penulis / Pengarang')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Nama Lengkap Penulis'),

                            Select::make('kategori_id')
                                ->label('Kategori')
                                ->relationship('kategori', 'nama_kategori')
                                ->required()
                                ->placeholder('Pilih Kategori'),

                            TextInput::make('penerbit')
                                ->label('Penerbit')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Nama Penerbit'),

                            TextInput::make('tahun_terbit')
                                ->label('Tahun Terbit')
                                ->required()
                                ->numeric()
                                ->minValue(1900)
                                ->maxValue((int) now()->format('Y') + 1)
                                ->placeholder(now()->format('Y')),

                            TextInput::make('jumlah_halaman')
                                ->label('Jumlah Halaman')
                                ->numeric()
                                ->minValue(1)
                                ->placeholder('0'),

                            TextInput::make('stok')
                                ->label('Stok Tersedia')
                                ->required()
                                ->numeric()
                                ->minValue(0)
                                ->placeholder('0'),

                            TextInput::make('lokasi_rak')
                                ->label('Lokasi Rak')
                                ->maxLength(50)
                                ->placeholder('Contoh: R-01-A (Sains)')
                                ->columnSpanFull(),

                            RichEditor::make('deskripsi')
                                ->label('Deskripsi / Sinopsis')
                                ->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'bulletList',
                                    'link',
                                ])
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 2,
                        ]),
                ])
                    ->from('lg')
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction()
                ->label('Batal'),
            $this->getSaveFormAction()
                ->label('Simpan Perubahan')
                ->icon('heroicon-m-check')
                ->color('primary'),
        ];
    }
}
