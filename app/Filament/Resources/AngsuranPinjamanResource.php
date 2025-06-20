<?php

// app/Filament/Resources/AngsuranPinjamanResource.php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\PinjamanUser;
use App\Models\AngsuranPinjaman;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkActionGroup;
use App\Filament\Resources\AngsuranPinjamanResource\Pages;

class AngsuranPinjamanResource extends Resource
{
    protected static ?string $model = AngsuranPinjaman::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Koperasi';
     protected static ?int $navigationSort = -11;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Select::make('id_pinjaman')
                ->options(
        \App\Models\PinjamanUser::with('user')->get()->pluck('user.name', 'id_pinjaman')
                )
                ->required(),

            TextInput::make('angsuran_ke')
                ->numeric()
                ->required(),

            DatePicker::make('tanggal_jatuh_tempo')->required(),

            DatePicker::make('tanggal_bayar'),

            TextInput::make('jumlah_bayar')
                ->numeric()
                ->required(),

            Select::make('status')
                ->options([
                    'belum' => 'Belum',
                    'sudah' => 'Sudah',
                    'terlambat' => 'Terlambat',
                ])
                ->required(),

            Textarea::make('keterangan'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            TextColumn::make('pinjaman.user.name')->label('Nama Peminjam'),
            TextColumn::make('angsuran_ke')->label('Angsuran ke'),
            TextColumn::make('tanggal_jatuh_tempo')->date()
                ->label('Tanggal Jatuh Tempo')
                ->sortable(),
            TextColumn::make('tanggal_bayar')->date(),
            TextColumn::make('jumlah_bayar')->money('IDR'),
            TextColumn::make('status')->badge()->color(fn ($state) => $state === 'sudah' ? 'success' : ($state === 'terlambat' ? 'danger' : 'warning')),
        ])
        ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'belum' => 'Belum',
                        'sudah' => 'Sudah',
                        'terlambat' => 'Terlambat',
                    ])
                    ->label('Status'),
            ])
            ->defaultSort('tanggal_jatuh_tempo', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAngsuranPinjaman::route('/'),
            'create' => Pages\CreateAngsuranPinjaman::route('/create'),
            'edit' => Pages\EditAngsuranPinjaman::route('/{record}/edit'),
        ];
    }
}
