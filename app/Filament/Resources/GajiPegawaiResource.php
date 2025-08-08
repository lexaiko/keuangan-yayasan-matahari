<?php

namespace App\Filament\Resources;

use App\Models\GajiPegawai;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use App\Filament\Resources\GajiPegawaiResource\Pages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class GajiPegawaiResource extends Resource
{
    protected static ?string $model = GajiPegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Gaji Pegawai';
    protected static ?string $pluralLabel = 'Gaji Pegawai';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Gaji')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Pegawai')
                            ->relationship('user', 'name', function ($query) {
                                return $query->where('is_pegawai', true);
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $userId = $get('user_id');
                                if ($userId) {
                                    $user = User::find($userId);
                                    $set('total_gaji', $user->gaji_bulanan);
                                }
                            }),

                        Forms\Components\Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                'Januari' => 'Januari',
                                'Februari' => 'Februari',
                                'Maret' => 'Maret',
                                'April' => 'April',
                                'Mei' => 'Mei',
                                'Juni' => 'Juni',
                                'Juli' => 'Juli',
                                'Agustus' => 'Agustus',
                                'September' => 'September',
                                'Oktober' => 'Oktober',
                                'November' => 'November',
                                'Desember' => 'Desember',
                            ])
                            ->default(Carbon::now()->locale('id')->translatedFormat('F'))
                            ->required(),

                        Forms\Components\Select::make('tahun')
                            ->label('Tahun')
                            ->options(function () {
                                $currentYear = date('Y');
                                $years = [];
                                for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++) {
                                    $years[$i] = $i;
                                }
                                return $years;
                            })
                            ->default(date('Y'))
                            ->required(),

                        Forms\Components\TextInput::make('total_gaji')
                            ->label('Total Gaji')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status & Keterangan')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'dibayar' => 'Dibayar',
                                'ditunda' => 'Ditunda',
                            ])
                            ->default('pending')
                            ->required()
                            ->live(),

                        Forms\Components\DatePicker::make('tanggal_bayar')
                            ->label('Tanggal Bayar')
                            ->visible(fn (callable $get) => $get('status') === 'dibayar'),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pegawai')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bulan')
                    ->label('Bulan')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_gaji')
                    ->label('Total Gaji')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'dibayar' => 'Dibayar',
                        'ditunda' => 'Ditunda',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'dibayar' => 'success',
                        'ditunda' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->date('d/m/Y')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name', function ($query) {
                        return $query->where('is_pegawai', true);
                    })
                    ->label('Pegawai')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'dibayar' => 'Dibayar',
                        'ditunda' => 'Ditunda',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('markAsPaid')
                    ->label('Bayar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (GajiPegawai $record): bool => $record->status !== 'dibayar')
                    ->requiresConfirmation()
                    ->modalHeading('Bayar Gaji')
                    ->modalDescription('Apakah Anda yakin ingin membayar gaji ini? Saldo yayasan akan berkurang.')
                    ->action(fn (GajiPegawai $record) => $record->update([
                        'status' => 'dibayar',
                        'tanggal_bayar' => now(),
                    ])),
                Tables\Actions\Action::make('revertPayment')
                    ->label('Batalkan Bayar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (GajiPegawai $record): bool => $record->status === 'dibayar')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Pembayaran Gaji')
                    ->modalDescription('Apakah Anda yakin ingin membatalkan pembayaran gaji ini? Saldo yayasan akan dikembalikan.')
                    ->action(fn (GajiPegawai $record) => $record->update([
                        'status' => 'pending',
                        'tanggal_bayar' => null,
                    ])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('markAsPaid')
                        ->label('Bayar Gaji Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Bayar Gaji Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin membayar semua gaji yang dipilih? Saldo yayasan akan berkurang.')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update([
                            'status' => 'dibayar',
                            'tanggal_bayar' => now(),
                        ]))),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('generateGaji')
                    ->label('Generate Gaji Bulan Ini')
                    ->icon('heroicon-o-plus-circle')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                'Januari' => 'Januari',
                                'Februari' => 'Februari',
                                'Maret' => 'Maret',
                                'April' => 'April',
                                'Mei' => 'Mei',
                                'Juni' => 'Juni',
                                'Juli' => 'Juli',
                                'Agustus' => 'Agustus',
                                'September' => 'September',
                                'Oktober' => 'Oktober',
                                'November' => 'November',
                                'Desember' => 'Desember',
                            ])
                            ->default(Carbon::now()->locale('id')->translatedFormat('F'))
                            ->required(),
                        Forms\Components\Select::make('tahun')
                            ->label('Tahun')
                            ->options(function () {
                                $currentYear = date('Y');
                                $years = [];
                                for ($i = $currentYear - 1; $i <= $currentYear + 1; $i++) {
                                    $years[$i] = $i;
                                }
                                return $years;
                            })
                            ->default(date('Y'))
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $employees = User::where('is_pegawai', true)->where('gaji_bulanan', '>', 0)->get();
                        $created = 0;

                        foreach ($employees as $employee) {
                            $exists = GajiPegawai::where([
                                'user_id' => $employee->id,
                                'bulan' => $data['bulan'],
                                'tahun' => $data['tahun'],
                            ])->exists();

                            if (!$exists) {
                                GajiPegawai::create([
                                    'user_id' => $employee->id,
                                    'bulan' => $data['bulan'],
                                    'tahun' => $data['tahun'],
                                    'total_gaji' => $employee->gaji_bulanan,
                                    'status' => 'pending',
                                ]);
                                $created++;
                            }
                        }

                        \Filament\Notifications\Notification::make()
                            ->title("Berhasil generate {$created} gaji pegawai")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Generate Gaji')
                    ->modalDescription('Generate gaji untuk semua pegawai pada bulan yang dipilih'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Pegawai')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Nama Pegawai'),
                        TextEntry::make('user.email')
                            ->label('Email'),
                        TextEntry::make('bulan')
                            ->label('Bulan')
                            ->badge()
                            ->color('info'),
                        TextEntry::make('tahun')
                            ->label('Tahun'),
                    ])
                    ->columns(2),

                Section::make('Detail Gaji')
                    ->schema([
                        TextEntry::make('total_gaji')
                            ->label('Total Gaji')
                            ->money('IDR')
                            ->weight('bold')
                            ->size('lg'),
                    ])
                    ->columns(1),

                Section::make('Status & Informasi Pembayaran')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'Pending',
                                'dibayar' => 'Dibayar',
                                'ditunda' => 'Ditunda',
                                default => ucfirst($state),
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'dibayar' => 'success',
                                'ditunda' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('tanggal_bayar')
                            ->label('Tanggal Bayar')
                            ->date('d F Y')
                            ->placeholder('Belum dibayar'),
                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Tidak ada keterangan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGajiPegawais::route('/'),
            'create' => Pages\CreateGajiPegawai::route('/create'),
            'view' => Pages\ViewGajiPegawai::route('/{record}'),
            'edit' => Pages\EditGajiPegawai::route('/{record}/edit'),
        ];
    }
}
