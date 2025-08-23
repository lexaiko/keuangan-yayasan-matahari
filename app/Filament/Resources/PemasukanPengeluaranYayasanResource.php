<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemasukanPengeluaranYayasanResource\Pages;
use App\Models\PemasukanPengeluaranYayasan;
use App\Models\KategoriPemasukanPengeluaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PemasukanPengeluaranYayasanResource extends Resource
{
    protected static ?string $model = PemasukanPengeluaranYayasan::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Manajemen Keuangan Yayasan';

    protected static ?string $navigationLabel = 'Transaksi Keuangan';

    protected static ?string $modelLabel = 'Transaksi Keuangan Yayasan';

    protected static ?string $pluralModelLabel = 'Transaksi Keuangan Yayasan';

    protected static ?int $navigationSort = -1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Transaksi')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('jenis_transaksi')
                                    ->label('Jenis Transaksi')
                                    ->options([
                                        'pemasukan' => 'Pemasukan',
                                        'pengeluaran' => 'Pengeluaran'
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(fn (Forms\Set $set) => $set('kategori_id', null))
                                    ->columnSpan(1),
                                Forms\Components\DatePicker::make('tanggal_transaksi')
                                    ->label('Tanggal Transaksi')
                                    ->required()
                                    ->default(now())
                                    ->columnSpan(1),
                            ]),
                        Forms\Components\Select::make('kategori_id')
                            ->label('Kategori')
                            ->options(function (Forms\Get $get) {
                                $jenis = $get('jenis_transaksi');
                                if (!$jenis) {
                                    return [];
                                }
                                return KategoriPemasukanPengeluaran::where('jenis', $jenis)
                                    ->where('is_aktif', true)
                                    ->pluck('nama_kategori', 'id')
                                    ->toArray();
                            })
                            ->required()
                            ->searchable()
                            ->live()
                            ->placeholder('Pilih jenis transaksi terlebih dahulu'),
                    ]),

                Forms\Components\Section::make('Detail Transaksi')
                    ->schema([
                        Forms\Components\TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0'),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->placeholder('Keterangan transaksi')
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_transaksi')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemasukan' => 'success',
                        'pengeluaran' => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategoriPemasukanPengeluaran.nama_kategori')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn ($record): string => $record->jenis_transaksi === 'pemasukan' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_transaksi')
                    ->label('Jenis Transaksi')
                    ->options([
                        'pemasukan' => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran'
                    ]),
                Tables\Filters\SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->options(KategoriPemasukanPengeluaran::pluck('nama_kategori', 'id'))
                    ->searchable(),
                Tables\Filters\Filter::make('tanggal_transaksi')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari_tanggal'], fn ($query, $date) => $query->whereDate('tanggal_transaksi', '>=', $date))
                            ->when($data['sampai_tanggal'], fn ($query, $date) => $query->whereDate('tanggal_transaksi', '<=', $date));
                    }),
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
            ])
            ->defaultSort('tanggal_transaksi', 'desc')
            ->striped();
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
            'index' => Pages\ListPemasukanPengeluaranYayasans::route('/'),
            'create' => Pages\CreatePemasukanPengeluaranYayasan::route('/create'),
            'view' => Pages\ViewPemasukanPengeluaranYayasan::route('/{record}'),
            'edit' => Pages\EditPemasukanPengeluaranYayasan::route('/{record}/edit'),
        ];
    }
}
