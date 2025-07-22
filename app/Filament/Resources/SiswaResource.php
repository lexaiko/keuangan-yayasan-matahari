<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Kelas;
use App\Models\Siswa;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\SiswaResource\Pages;
use Filament\Tables\Columns\{TextColumn, ImageColumn};
use Filament\Tables\Actions\{EditAction, DeleteAction, DeleteBulkAction};
use Filament\Forms\Components\{TextInput, Select, DatePicker, Textarea, FileUpload};
use Illuminate\Support\Collection;


class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Data Siswa';
    protected static ?string $pluralLabel = 'Siswa';
    protected static ?int $navigationSort = -10;
    protected static ?string $navigationGroup = 'Master Data';

    public static function getSlug(): string
    {
        return 'data-siswa'; // Ganti dengan slug URL yang kamu mau
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('foto')
                    ->label('Foto Siswa')
                    ->image()
                    ->directory('foto-siswa')
                    ->imageEditor(),
                TextInput::make('nama')->required()->maxLength(255),
                Select::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('nis')->maxLength(100)->unique(),
                TextInput::make('nisn')->maxLength(100)->unique(),
                TextInput::make('nik')->maxLength(100)->unique(),
                TextInput::make('tempat_lahir')->maxLength(100),
                DatePicker::make('tanggal_lahir'),
                Select::make('jenis_kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->required(),
                Textarea::make('alamat')->rows(3),
                TextInput::make('nama_ayah')->maxLength(255),
                TextInput::make('nama_ibu')->maxLength(255),
                TextInput::make('telepon')->tel(),
                TextInput::make('email')->email(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '2' => 'Baru',
                        '3' => 'Pindahan',
                        '4' => 'keluar',
                        '5' => 'Lulus',
                    ])
                    ->default('1')
                    ->required()
                    ->searchable()
                    ->preload(),
            ])
            ->columns(2); // Membagi form jadi 2 kolom
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')->label('Foto')->circular(),
                TextColumn::make('nama')->searchable()->sortable(),
                TextColumn::make('nis'),
                TextColumn::make('nisn'),
                TextColumn::make('ttl') // bebas nama kolom ini
                    ->label('TTL')
                    ->getStateUsing(function ($record) {
                        return $record->tempat_lahir . ', ' . Carbon::parse($record->tanggal_lahir)->translatedFormat('d F Y');
                    }),
                TextColumn::make('jenis_kelamin')
                    ->formatStateUsing(fn(string $state): string => ['L' => 'Laki-laki', 'P' => 'Perempuan'][$state]),
                TextColumn::make('kelas.nama')->label('Kelas')->sortable(),
                TextColumn::make('alamat')->limit(50),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => match ($state) {
                        '1' => 'Aktif',
                        '2' => 'Baru',
                        '3' => 'Pindahan',
                        '4' => 'Keluar',
                        '5' => 'Lulus',
                        default => 'Tidak Diketahui',
                    })
                    ->color(fn($state) => match ($state) {
                        '1' => 'success',
                        '2' => 'secondary',
                        '3' => 'info',
                        '4' => 'danger',
                        '5' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('kelas_id')
                    ->relationship('kelas', 'nama', modifyQueryUsing: function ($query) {
                        $query->whereHas('tahun', fn($q) => $q->where('is_active', true));
                    })
                    ->searchable()
                    ->label('Kelas'),
                SelectFilter::make('status')
                    ->options([
                        '1' => 'Aktif',
                        '2' => 'Baru',
                        '3' => 'Pindahan',
                        '4' => 'Keluar',
                        '5' => 'Lulus',
                    ])
                    ->label('Status'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
    BulkAction::make('ubahStatus')
        ->label('Ubah Status')
        ->form([
            Select::make('status')
                ->label('Status')
                ->options([
                    '1' => 'Aktif',
                    '2' => 'Baru',
                    '3' => 'Pindahan',
                    '4' => 'keluar',
                    '5' => 'Lulus',
                ])
                ->default('1')
                ->required()
                ->searchable()
                ->preload(),
        ])
        ->action(function (Collection $records, array $data) {
    foreach ($records as $record) {
        $record->update([
            'status' => $data['status'],
        ]);
    }
        })
        ->deselectRecordsAfterCompletion()
        ->requiresConfirmation()
        ->icon('heroicon-m-arrow-path-rounded-square') // opsional
        ->color('warning'), // opsional
            DeleteBulkAction::make()
])
            ->recordUrl(fn(Siswa $record): string => static::getUrl('show', ['record' => $record]));
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make(4)
                    ->schema([
                        Grid::make()
                            ->schema([
                                ImageEntry::make('foto')
                                    ->label('')
                                    ->width(170)
                                    ->height(230),
                            ])->columnSpan([
                                'sm' => 4,
                                'md' => 1
                            ]),
                        Section::make()
                            ->schema([
                                TextEntry::make('nama')
                                    ->weight('bold'),
                                TextEntry::make('nis')
                                    ->label('NIS')
                                    ->weight('bold')
                                    ->placeholder('NIS belum diisi'),
                                TextEntry::make('nisn')
                                    ->label('NISN')
                                    ->weight('bold')
                                    ->placeholder('NISN belum diisi'),
                                TextEntry::make('jenis_kelamin')
                                    ->formatStateUsing(fn(string $state): string => ['L' => 'Laki-laki', 'P' => 'Perempuan'][$state])
                                    ->weight('bold'),
                                TextEntry::make('nik')
                                    ->label('NIK')
                                    ->weight('bold'),
                                TextEntry::make('tempat_lahir')
                                    ->weight('bold'),
                                TextEntry::make('tanggal_lahir')
                                    ->date('d F Y')
                                    ->weight('bold'),
                                TextEntry::make('alamat')
                                    ->columnSpanFull()
                                    ->weight('bold'),
                                TextEntry::make('nama_ayah')
                                    ->placeholder('Nama Ayah belum diisi')
                                    ->weight('bold'),
                                TextEntry::make('nama_ibu')
                                    ->weight('bold'),
                                TextEntry::make('telepon')
                                    ->weight('bold'),
                                TextEntry::make('email')
                                    ->placeholder('Email belum diisi')
                                    ->weight('bold'),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(fn(string $state) => match ($state) {
                                        '1' => 'Aktif',
                                        '2' => 'Baru',
                                        '3' => 'Pindahan',
                                        '4' => 'keluar',
                                        '5' => 'Lulus',
                                        default => 'Tidak diketahui',
                                    })
                                    ->color(fn(string $state): string => match ($state) {
                                        '1' => 'success',
                                        '2' => 'warning',
                                        '3' => 'info',
                                        '4' => 'danger',
                                        '5' => 'info',
                                        default => 'gray',
                                    }),
                            ])
                            ->columns(2)
                            ->columnSpan([
                                'sm' => 4,
                                'md' => 3
                            ]),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
            'show' => Pages\ViewSiswa::route('/{record}'),
        ];
    }
}

