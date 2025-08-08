<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelasResource\Pages;
use App\Filament\Resources\KelasResource\RelationManagers;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationLabel = 'Data Kelas';
    protected static ?string $pluralLabel = 'Data Kelas';
    protected static ?int $navigationSort = -9;
    protected static ?string $navigationGroup = 'Master Data';
    public static function getSlug(): string
{
    return 'data-kelas'; // Ganti dengan slug URL yang kamu mau
}


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Kelas')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('tingkat_id')
                    ->label('Tingkat Kelas')
                    ->relationship('tingkat', 'nama')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('tahun_id')
                    ->label('Tahun Akademik')
                    ->relationship('tahun', 'nama')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Kelas')
                    ->schema([
                        TextEntry::make('nama')
                            ->label('Nama Kelas'),
                        TextEntry::make('tingkat.nama')
                            ->label('Tingkat Kelas'),
                        TextEntry::make('tahun.nama')
                            ->label('Tahun Akademik'),
                        TextEntry::make('siswas_count')
                            ->label('Jumlah Siswa')
                            ->getStateUsing(fn ($record) => $record->siswas()->count() . ' siswa'),
                    ])
                    ->columns(2),

                Section::make('Daftar Siswa')
                    ->schema([
                        RepeatableEntry::make('siswas')
                            ->label('')
                            ->schema([
                                TextEntry::make('nis')
                                    ->label('NIS')
                                    ->weight('bold'),
                                TextEntry::make('nama')
                                    ->label('Nama Siswa'),
                                TextEntry::make('jenis_kelamin')
                                    ->label('Jenis Kelamin')
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                        default => $state,
                                    }),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'aktif' => 'success',
                                        'tidak_aktif' => 'danger',
                                        'lulus' => 'info',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),
                            ])
                            ->columns(4)
                            ->contained(false)
                            ->getStateUsing(function ($record) {
                                return $record->siswas()
                                    ->orderBy('nama')
                                    ->get()
                                    ->map(function ($siswa) {
                                        return [
                                            'nis' => $siswa->nis,
                                            'nama' => $siswa->nama,
                                            'jenis_kelamin' => $siswa->jenis_kelamin,
                                            'status' => $siswa->status,
                                        ];
                                    });
                            }),
                    ])
                    ->visible(fn ($record) => $record->siswas()->exists()),

                Section::make('Informasi Tambahan')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Diperbarui Pada')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record): string => KelasResource::getUrl('view', ['record' => $record]))
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tingkat.nama')
                    ->label('Tingkat Kelas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tahun.nama')
                    ->label('Tahun Akademik')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('siswas_count')
                    ->label('Jumlah Siswa')
                    ->counts('siswas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable(),
                //
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tingkat_id')
                    ->relationship('tingkat', 'nama')
                    ->label('Tingkat Kelas')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('tahun_id')
                    ->relationship('tahun', 'nama')
                    ->label('Tahun Akademik')
                    ->searchable()
                    ->preload(),
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
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'view' => Pages\ViewKelas::route('/{record}'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
        ];
    }
}
