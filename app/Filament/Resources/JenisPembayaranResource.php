<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\JenisPembayaran;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\JenisPembayaranResource\Pages;
use App\Filament\Resources\JenisPembayaranResource\RelationManagers;

class JenisPembayaranResource extends Resource
{
    protected static ?string $model = JenisPembayaran::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Jenis Pembayaran';
    protected static ?string $navigationGroup = 'Manajemen Tagihan';
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama_pembayaran')->required(),
            TextInput::make('nominal')
    ->numeric()
    ->prefix('Rp ')
    ->inputMode('decimal'),
            Select::make('tipe')
                ->options([
                    'bulanan' => 'Bulanan',
                    'sekali' => 'Sekali',
                    'bebas' => 'Bebas',
                ])
                ->required(),
            Toggle::make('aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('nama_pembayaran'),
            TextColumn::make('nominal')->money('IDR'),
            TextColumn::make('tipe'),
            ToggleColumn::make('aktif') // ini bisa diubah langsung
        ->label('Status Aktif')
        ->onColor('success')
        ->offColor('danger'),
        ])
            ->filters([
                //
            ])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJenisPembayarans::route('/'),
            'create' => Pages\CreateJenisPembayaran::route('/create'),
            'edit' => Pages\EditJenisPembayaran::route('/{record}/edit'),
        ];
    }
}
