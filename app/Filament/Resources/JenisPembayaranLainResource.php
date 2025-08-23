<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenisPembayaranLainResource\Pages;
use App\Models\JenisPembayaranLain;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JenisPembayaranLainResource extends Resource
{
    protected static ?string $model = JenisPembayaranLain::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Jenis Pembayaran Lain';

    protected static ?string $pluralModelLabel = 'Jenis Pembayaran Lain';

    protected static ?string $navigationGroup = 'Pembayaran';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_jenis')
                    ->label('Nama Jenis Pembayaran')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Donasi, Infaq, dll'),
                Forms\Components\Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->placeholder('Deskripsi singkat tentang jenis pembayaran ini')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_aktif')
                    ->label('Status Aktif')
                    ->default(true)
                    ->helperText('Hanya jenis pembayaran aktif yang bisa dipilih'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_jenis')
                    ->label('Nama Jenis')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_aktif')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pembayaranLain_count')
                    ->label('Jumlah Pembayaran')
                    ->counts('pembayaranLain')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_aktif')
                    ->label('Status Aktif')
                    ->boolean()
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListJenisPembayaranLains::route('/'),
            'create' => Pages\CreateJenisPembayaranLain::route('/create'),
            'view' => Pages\ViewJenisPembayaranLain::route('/{record}'),
            'edit' => Pages\EditJenisPembayaranLain::route('/{record}/edit'),
        ];
    }
}
