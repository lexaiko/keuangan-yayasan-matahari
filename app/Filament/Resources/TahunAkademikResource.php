<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\TahunAkademik;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TahunAkademikResource\Pages;
use App\Filament\Resources\TahunAkademikResource\RelationManagers;

class TahunAkademikResource extends Resource
{
    protected static ?string $model = TahunAkademik::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Tahun Akademik ';
    protected static ?string $pluralLabel = 'Tahun Akademik';
    protected static ?int $navigationSort = -7;
    protected static ?string $navigationGroup = 'Master Data';
    public static function getSlug(): string
{
    return 'tahun-akademik'; // Ganti dengan slug URL yang kamu mau
}


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->label('Nama Tahun Akademik')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->placeholder('Masukkan nama tahun akademik, misal "2023/2024"'),
                Toggle::make('is_active')
                    ->label('Aktifkan Tahun Akademik'),
                DatePicker::make('mulai')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->placeholder('Pilih tanggal mulai tahun akademik'),

                DatePicker::make('selesai')
                    ->label('Tanggal Selesai')
                    ->required()
                    ->placeholder('Pilih tanggal selesai tahun akademik'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Tahun Akademik')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mulai')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('selesai')
                    ->label('Tanggal Selesai')
                    ->date()
                    ->sortable(),

                ToggleColumn::make('is_active')
                ->label('Aktif?')
                ->sortable()
                ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Aktif')
                    ->options([
                        '1' => 'Ya',
                        '0' => 'Tidak',
                    ])
                    ->default('1'),
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
            'index' => Pages\ListTahunAkademiks::route('/'),
            'create' => Pages\CreateTahunAkademik::route('/create'),
            'edit' => Pages\EditTahunAkademik::route('/{record}/edit'),
        ];
    }
}

