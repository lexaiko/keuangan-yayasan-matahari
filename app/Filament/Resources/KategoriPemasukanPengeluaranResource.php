<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriPemasukanPengeluaranResource\Pages;
use App\Filament\Resources\KategoriPemasukanPengeluaranResource\RelationManagers;
use App\Models\KategoriPemasukanPengeluaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KategoriPemasukanPengeluaranResource extends Resource
{
    protected static ?string $model = KategoriPemasukanPengeluaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Manajemen Keuangan Yayasan';

    protected static ?string $navigationLabel = 'Kategori Keuangan';

    protected static ?string $modelLabel = 'Kategori Pemasukan/Pengeluaran';

    protected static ?string $pluralModelLabel = 'Kategori Pemasukan/Pengeluaran';

    protected static ?int $navigationSort = -2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_kategori')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Kategori'),
                Forms\Components\Select::make('jenis')
                    ->options([
                        'pemasukan' => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran',
                    ])
                    ->required()
                    ->label('Jenis'),
                Forms\Components\Textarea::make('deskripsi')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('Deskripsi'),
                Forms\Components\Toggle::make('is_aktif')
                    ->default(true)
                    ->label('Status Aktif'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kategori')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Kategori'),
                Tables\Columns\BadgeColumn::make('jenis')
                    ->colors([
                        'success' => 'pemasukan',
                        'danger' => 'pengeluaran',
                    ])
                    ->sortable()
                    ->label('Jenis'),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->label('Deskripsi'),
                Tables\Columns\IconColumn::make('is_aktif')
                    ->boolean()
                    ->label('Status Aktif'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Diperbarui'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->options([
                        'pemasukan' => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran',
                    ])
                    ->label('Jenis'),
                Tables\Filters\TernaryFilter::make('is_aktif')
                    ->label('Status Aktif'),
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
            'index' => Pages\ListKategoriPemasukanPengeluarans::route('/'),
            'create' => Pages\CreateKategoriPemasukanPengeluaran::route('/create'),
            'view' => Pages\ViewKategoriPemasukanPengeluaran::route('/{record}'),
            'edit' => Pages\EditKategoriPemasukanPengeluaran::route('/{record}/edit'),
        ];
    }
}
