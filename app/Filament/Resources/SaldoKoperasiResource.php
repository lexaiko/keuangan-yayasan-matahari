<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\SaldoKoperasi;
use Filament\Resources\Resource;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use App\Filament\Resources\SaldoKoperasiResource\Pages;
use Filament\Forms\Components\{DatePicker, Select, TextInput, Textarea};

class SaldoKoperasiResource extends Resource
{
    protected static ?string $model = SaldoKoperasi::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Koperasi';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Select::make('pelaku_terkait_id')
                ->relationship('pelakuTerkait', 'name')
                ->label('Pelaku Terkait')
                ->required()
                ->searchable()
                ->preload(),
            TextInput::make('kategori')
                ->default('tabungan')
                ->label('Kategori')
                ->disabled()
                ->required(),
            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->default(now())
                ->required(),
            Select::make('tipe')
                ->options([
                    'masuk' => 'Masuk',
                    'keluar' => 'Keluar',
                ])
                ->label('Uang Masuk/Keluar')
                ->required(),

            TextInput::make('jumlah')
                ->numeric()
                ->prefix('Rp ')
                ->required(),

            Textarea::make('keterangan')->rows(2),
        ]);

        ;
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            TextColumn::make('pelakuTerkait.name')->label('Pelaku Terkait')->searchable()->sortable(),
            TextColumn::make('kategori')
                ->badge()
                ->color(fn ($state) => $state === 'tabungan' ? 'primary' : ($state === 'pinjaman' ? 'secondary' : 'warning')),
            TextColumn::make('tanggal')->date()
                ->label('Tanggal')
                ->sortable(),
            TextColumn::make('tipe')
                ->badge()
                ->color(fn ($state) => $state === 'masuk' ? 'success' : 'danger'),
            TextColumn::make('jumlah')->money('IDR'),
            TextColumn::make('keterangan')->wrap(),
        ])->defaultSort('created_at', 'desc')  // Changed from 'desc' to 'asc' for newest data first
        ->filters([
            SelectFilter::make('tipe')
                ->options([
                    'masuk' => 'Masuk',
                    'keluar' => 'Keluar',
                ])
                ->label('Filter Tipe')
                ->placeholder('Pilih Tipe'),
            SelectFilter::make('pelaku_terkait_id')
                ->relationship('pelakuTerkait', 'name')
                ->label('Filter Pelaku Terkait')
                ->placeholder('Pilih Pelaku'),
        ])
        //filter desc
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
         ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSaldoKoperasis::route('/'),
            'create' => Pages\CreateSaldoKoperasi::route('/create'),
            'edit' => Pages\EditSaldoKoperasi::route('/{record}/edit'),
        ];
    }
}
