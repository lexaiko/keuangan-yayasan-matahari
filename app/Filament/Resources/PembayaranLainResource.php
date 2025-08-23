<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembayaranLainResource\Pages;
use App\Models\PembayaranLain;
use App\Models\JenisPembayaranLain;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PembayaranLainResource extends Resource
{
    protected static ?string $model = PembayaranLain::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Pembayaran Lain-Lain';

    protected static ?string $pluralModelLabel = 'Pembayaran Lain-Lain';

    protected static ?string $navigationGroup = 'Pembayaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pembayaran')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('jenis_pembayaran_lain_id')
                                    ->label('Jenis Pembayaran')
                                    ->options(JenisPembayaranLain::where('is_aktif', true)->pluck('nama_jenis', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->columnSpan(1),
                                Forms\Components\DatePicker::make('tanggal_pembayaran')
                                    ->label('Tanggal Pembayaran')
                                    ->required()
                                    ->default(now())
                                    ->columnSpan(1),
                            ]),
                    ]),

                Forms\Components\Section::make('Data Pembayar')
                    ->schema([
                        Forms\Components\Select::make('siswa_id')
                            ->label('Pilih Siswa')
                            ->options(function () {
                                try {
                                    return Siswa::all()->pluck('nama', 'id');
                                } catch (\Exception $e) {
                                    return [];
                                }
                            })
                            ->searchable()
                            ->nullable()
                            ->placeholder('Cari nama siswa...')
                            ->helperText('Kosongkan jika bukan dari siswa')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    try {
                                        $siswa = Siswa::find($state);
                                        if ($siswa) {
                                            $set('nama_pembayar', $siswa->nama);
                                        }
                                    } catch (\Exception $e) {
                                        // Handle error silently
                                    }
                                } else {
                                    $set('nama_pembayar', '');
                                }
                            }),
                        Forms\Components\TextInput::make('nama_pembayar')
                            ->label('Nama Pembayar')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama pembayar')
                            ->helperText('Akan terisi otomatis jika memilih siswa'),
                    ]),

                Forms\Components\Section::make('Detail Pembayaran')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('jumlah')
                                    ->label('Jumlah Pembayaran')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('0')
                                    ->live(onBlur: true)
                                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, ',', '.') : '')
                                    ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace(['.', ','], '', $state) : 0)
                                    ->columnSpan(1),
                                Forms\Components\Placeholder::make('jumlah_display')
                                    ->label('Total Pembayaran')
                                    ->live()
                                    ->content(function ($get) {
                                        $jumlah = $get('jumlah');
                                        if ($jumlah) {
                                            $clean = (int) str_replace(['.', ',', 'Rp', ' '], '', $jumlah);
                                            if ($clean > 0) {
                                                return 'Rp ' . number_format($clean, 0, ',', '.');
                                            }
                                        }
                                        return 'Rp 0';
                                    })
                                    ->columnSpan(1),
                            ]),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->placeholder('Catatan tambahan (opsional)')
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_pembayaran')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenisPembayaranLain.nama_jenis')
                    ->label('Jenis Pembayaran')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_pembayar')
                    ->label('Nama Pembayar')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(20)
                    ->tooltip(function ($record) {
                        return $record->keterangan;
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_pembayaran_lain_id')
                    ->label('Jenis Pembayaran')
                    ->options(JenisPembayaranLain::pluck('nama_jenis', 'id'))
                    ->multiple(),
                Tables\Filters\SelectFilter::make('siswa_id')
                    ->label('Siswa')
                    ->options(function () {
                        try {
                            return Siswa::pluck('nama', 'id');
                        } catch (\Exception $e) {
                            return [];
                        }
                    })
                    ->searchable()
                    ->multiple(),
                Tables\Filters\Filter::make('tanggal_pembayaran')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari_tanggal'], fn ($query, $date) => $query->whereDate('tanggal_pembayaran', '>=', $date))
                            ->when($data['sampai_tanggal'], fn ($query, $date) => $query->whereDate('tanggal_pembayaran', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\Action::make('print_invoice')
                    ->label('Print Invoice')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn ($record) => route('pembayaran-lain.print-invoice', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ]),
            ])
            ->defaultSort('tanggal_pembayaran', 'desc')
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
            'index' => Pages\ListPembayaranLains::route('/'),
            'create' => Pages\CreatePembayaranLain::route('/create'),
            'view' => Pages\ViewPembayaranLain::route('/{record}'),
            'edit' => Pages\EditPembayaranLain::route('/{record}/edit'),
        ];
    }
}
