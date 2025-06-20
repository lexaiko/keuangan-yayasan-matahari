<?php

// app/Filament/Resources/PinjamanUserResource.php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\PinjamanUser;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use App\Filament\Resources\PinjamanUserResource\Pages;

class PinjamanUserResource extends Resource
{
    protected static ?string $model = PinjamanUser::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Koperasi';
    protected static ?int $navigationSort = -12;
    protected static ?string $navigationLabel = 'Pinjaman Pegawai';
    protected static ?string $slug = 'pinjaman-pegawai';
    protected static ?string $pluralLabel = 'Pinjaman Pegawai';


     protected static function hitungTotal(callable $set, callable $get): void
{
    $jumlah = floatval($get('jumlah_pinjam') ?? 0);
    $bunga  = floatval($get('bunga_persen') ?? 0);
    $total  = $jumlah + ($jumlah * $bunga / 100);

    $set('total_kembali', round($total, 2));
}

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Select::make('user_id')
                ->relationship('user', 'name')
                ->label('Peminjam')
                ->required(),

            DatePicker::make('tanggal_pinjam')
                ->label('Tanggal Pinjam')
                ->required(),

            TextInput::make('jumlah_pinjam')
    ->label('Jumlah Pinjam')
    ->numeric()
    ->prefix('IDR ')
    ->required()
    ->reactive()
    ->live(onBlur: true)
    ->afterStateUpdated(function (callable $set, callable $get) {
        static::hitungTotal($set, $get);
    }),

TextInput::make('bunga_persen')
    ->label('Bunga (%)')
    ->numeric()
    ->default(0)
    ->reactive()
    ->live(onBlur: true)
    ->afterStateUpdated(function (callable $set, callable $get) {
        static::hitungTotal($set, $get);
    }),

TextInput::make('total_kembali')
    ->label('Total Kembali')
    ->numeric()
    ->prefix('IDR ')
    ->disabled()     // readonly
    ->dehydrated()   // tetap disimpan
    ->default(0),



            TextInput::make('tenor_bulan')
                ->label('Tenor (bulan)')
                ->numeric()
                ->required(),

            Select::make('status')
                ->options([
                    'berjalan' => 'Berjalan',
                    'lunas' => 'Lunas',
                ])
                ->required(),

            Textarea::make('catatan')->label('Catatan'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            TextColumn::make('user.name')->label('Nama'),
            TextColumn::make('tanggal_pinjam')->date(),
            TextColumn::make('jumlah_pinjam')->money('IDR'),
            TextColumn::make('total_kembali')->money('IDR'),
            TextColumn::make('tenor_bulan')->label('Tenor (bulan)'),
            TextColumn::make('status')->badge()->color(fn ($state) => $state === 'lunas' ? 'success' : 'warning'),
        ])
        ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(fn(PinjamanUser $record): string => static::getUrl('show', ['record' => $record]));
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPinjamanUsers::route('/'),
            'create' => Pages\CreatePinjamanUser::route('/create'),
            'edit' => Pages\EditPinjamanUser::route('/{record}/edit'),
            'show' => Pages\ViewPinjamanUser::route('/{record}'),
        ];
    }
}
