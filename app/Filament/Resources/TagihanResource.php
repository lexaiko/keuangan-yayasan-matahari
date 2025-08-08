<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Tagihan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\JenisPembayaran;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\TagihanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TagihanResource\RelationManagers;

class TagihanResource extends Resource
{
    protected static ?string $model = Tagihan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Manajemen Tagihan';

    public static function form(Form $form): Form
    {
        return $form->schema([
        Select::make('siswa_id')
    ->relationship('siswa', 'nama')
    ->searchable()
    ->required()
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set) {
        $siswa = \App\Models\Siswa::with('kelas.tahun')->find($state);

        if ($siswa && $siswa->kelas && $siswa->kelas->tahun) {
            $set('tahun_akademik_id', $siswa->kelas->tahun->id);
        }
    }),


        Select::make('jenis_pembayaran_id')
            ->relationship('jenisPembayaran', 'nama_pembayaran')
            ->required()
            ->reactive()
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $jenis = JenisPembayaran::find($state);

                // Set jumlah otomatis dari nominal
                $set('jumlah', $jenis?->nominal ?? 0);

                // Optional: reset 'bulan' kalau tipe pembayaran bukan bulanan
                if ($jenis?->tipe_pembayaran === 'sekali') {
                    $set('bulan', null);
                }
            }),

        Hidden::make('tahun_akademik_id')
    ->required(),

        Select::make('bulan')
            ->required()
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
            ->native(false)
            ->visible(function (callable $get) {
                $jenis = JenisPembayaran::find($get('jenis_pembayaran_id'));
                return $jenis?->tipe === 'bulanan';
            }),

        TextInput::make('jumlah')
            ->numeric()
            ->required(),

        DatePicker::make('tanggal_jatuh_tempo')
            ->nullable(),
    ]);
    }

    public static function infolist(Infolist $infolist): Infolist
{
    return $infolist->schema([
        TextEntry::make('siswa.nis')->label('NIS Siswa'),
        TextEntry::make('siswa.nama')->label('Nama Siswa'),
        TextEntry::make('jenisPembayaran.nama_pembayaran')->label('Jenis Pembayaran'),
        TextEntry::make('tahunAkademik.nama')->label('Tahun Akademik'),
        TextEntry::make('bulan')->label('Bulan')->hidden(fn ($record) => $record->bulan === null),
        TextEntry::make('jumlah')->label('Jumlah')->money('IDR'),
        TextEntry::make('status')
    ->badge()
    ->color(fn (string $state): string => match ($state) {
        'sebagian' => 'warning',
        'lunas' => 'success',
        'belum_bayar' => 'danger',
    }),
    ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record): string => TagihanResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('siswa.nis')->label('NIS Siswa')->searchable(),
                TextColumn::make('siswa.nama')->label('Siswa')->searchable(),
                Tables\Columns\TextColumn::make('jenisPembayaran.nama_pembayaran')->label('Jenis'),
                Tables\Columns\TextColumn::make('tahunAkademik.nama')->label('Tahun'),
                Tables\Columns\TextColumn::make('bulan'),
                Tables\Columns\TextColumn::make('jumlah')->money('IDR'),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'lunas' => 'Lunas',
                        'belum_bayar' => 'Belum Bayar',
                        'sebagian' => 'Sebagian',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'lunas' => 'success',
                        'belum_bayar' => 'danger',
                        'sebagian' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('jenis_pembayaran_id')
                    ->relationship('jenisPembayaran', 'nama_pembayaran')
                    ->label('Jenis Pembayaran'),
                SelectFilter::make('siswa_id')
                    ->relationship('siswa', 'nama')
                    ->label('Siswa')
                    ->searchable(),
                SelectFilter::make('status')
                    ->options([
                        'belum_bayar' => 'Belum Bayar',
                        'lunas' => 'Lunas',
                    ])
                    ->label('Status'),
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
            'index' => Pages\ListTagihans::route('/'),
            'create' => Pages\CreateTagihan::route('/create'),
            'edit' => Pages\EditTagihan::route('/{record}/edit'),
            'view' => Pages\ViewTagihan::route('/{record}'),
        ];
    }
}
