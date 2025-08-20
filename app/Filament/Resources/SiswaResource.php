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
use App\Exports\SiswaExport;
use Maatwebsite\Excel\Facades\Excel;
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
                Tables\Actions\Action::make('viewTagihan')
                    ->label('View Tagihan')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('info')
                    ->modalHeading(fn ($record) => 'Tagihan - ' . $record->nama)
                    ->modalContent(function ($record) {
                        $tagihans = \App\Models\Tagihan::where('siswa_id', $record->id)
                            ->where('status', '!=', \App\Models\Tagihan::STATUS_LUNAS)
                            ->with(['jenisPembayaran', 'detailPembayarans'])
                            ->orderBy('tanggal_jatuh_tempo')
                            ->get();

                        if ($tagihans->isEmpty()) {
                            return new \Illuminate\Support\HtmlString('
                                <div class="text-center py-8">
                                    <div class="text-gray-400 text-lg mb-2">✅</div>
                                    <p class="text-gray-600">Tidak ada tagihan yang belum lunas</p>
                                </div>
                            ');
                        }

                        $html = '<div class="space-y-4">';

                        foreach ($tagihans as $tagihan) {
                            $totalDibayar = $tagihan->detailPembayarans->sum('jumlah_bayar');
                            $sisaTagihan = $tagihan->jumlah - $totalDibayar;

                            $statusColor = match($tagihan->status) {
                                \App\Models\Tagihan::STATUS_BELUM_BAYAR => 'bg-red-100 text-red-800',
                                \App\Models\Tagihan::STATUS_SEBAGIAN => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-gray-100 text-gray-800'
                            };

                            $statusText = match($tagihan->status) {
                                \App\Models\Tagihan::STATUS_BELUM_BAYAR => 'Belum Bayar',
                                \App\Models\Tagihan::STATUS_SEBAGIAN => 'Sebagian',
                                default => ucfirst(str_replace('_', ' ', $tagihan->status))
                            };

                            $isOverdue = $tagihan->tanggal_jatuh_tempo && \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->isPast();
                            $overdueClass = $isOverdue ? 'border-red-300 bg-red-50' : 'border-gray-200';

                            $html .= '
                                <div class="border rounded-lg p-4 ' . $overdueClass . '">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-semibold text-gray-900">' .
                                            $tagihan->jenisPembayaran->nama_pembayaran .
                                            ($tagihan->bulan ? ' - ' . $tagihan->bulan : '') .
                                        '</h4>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium ' . $statusColor . '">' .
                                            $statusText .
                                        '</span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-600">Total Tagihan:</span>
                                            <div class="font-semibold">Rp ' . number_format($tagihan->jumlah, 0, ',', '.') . '</div>
                                        </div>';

                            if ($totalDibayar > 0) {
                                $html .= '
                                        <div>
                                            <span class="text-gray-600">Sudah Dibayar:</span>
                                            <div class="font-semibold text-green-600">Rp ' . number_format($totalDibayar, 0, ',', '.') . '</div>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Sisa Tagihan:</span>
                                            <div class="font-semibold text-red-600">Rp ' . number_format($sisaTagihan, 0, ',', '.') . '</div>
                                        </div>';
                            }

                            if ($tagihan->tanggal_jatuh_tempo) {
                                $dueDate = \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo);
                                $dueDateClass = $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-900';

                                $html .= '
                                        <div>
                                            <span class="text-gray-600">Jatuh Tempo:</span>
                                            <div class="' . $dueDateClass . '">' .
                                                $dueDate->format('d/m/Y') .
                                                ($isOverdue ? ' (Terlambat)' : '') .
                                            '</div>
                                        </div>';
                            }

                            $html .= '
                                    </div>';

                            if ($tagihan->keterangan) {
                                $html .= '
                                    <div class="mt-2 text-sm text-gray-600">
                                        <span class="font-medium">Keterangan:</span> ' . $tagihan->keterangan . '
                                    </div>';
                            }

                            $html .= '</div>';
                        }

                        $totalSemua = $tagihans->sum(fn($t) => $t->jumlah - $t->detailPembayarans->sum('jumlah_bayar'));

                        $html .= '
                            </div>
                            <div class="mt-6 pt-4 border-t">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total Semua Tagihan:</span>
                                    <span class="text-xl font-bold text-red-600">Rp ' . number_format($totalSemua, 0, ',', '.') . '</span>
                                </div>
                            </div>';

                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalFooterActions([
                        Tables\Actions\Action::make('printTagihan')
                            ->label('Print PDF')
                            ->icon('heroicon-o-printer')
                            ->color('success')
                            ->url(fn ($record) => route('tagihan.print', ['siswa_id' => $record->id]))
                            ->openUrlInNewTab()
                            ->visible(fn ($record) => \App\Models\Tagihan::where('siswa_id', $record->id)
                                ->where('status', '!=', \App\Models\Tagihan::STATUS_LUNAS)
                                ->exists())
                    ]),
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
                BulkAction::make('exportSelected')
                    ->label('Export Selected to Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (Collection $records) {
                        $siswaIds = $records->pluck('id')->toArray();
                        $siswas = Siswa::with(['kelas.tingkat', 'kelas.tahun'])
                            ->whereIn('id', $siswaIds)
                            ->orderBy('nama')
                            ->get();

                        return Excel::download(new class($siswas) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\ShouldAutoSize {
                            private $siswas;

                            public function __construct($siswas)
                            {
                                $this->siswas = $siswas;
                            }

                            public function collection()
                            {
                                return $this->siswas;
                            }

                            public function headings(): array
                            {
                                return [
                                    'No', 'Nama', 'NIS', 'NISN', 'NIK', 'Tempat Lahir', 'Tanggal Lahir',
                                    'Jenis Kelamin', 'Alamat', 'Nama Ayah', 'Nama Ibu', 'Telepon', 'Email',
                                    'Kelas', 'Tingkat', 'Tahun Akademik', 'Status'
                                ];
                            }

                            public function map($siswa): array
                            {
                                static $no = 1;
                                $status = match ($siswa->status) {
                                    '1' => 'Aktif', '2' => 'Baru', '3' => 'Pindahan',
                                    '4' => 'Keluar', '5' => 'Lulus', default => 'Tidak Diketahui',
                                };
                                $jenisKelamin = $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';

                                return [
                                    $no++, $siswa->nama, $siswa->nis, $siswa->nisn, $siswa->nik,
                                    $siswa->tempat_lahir, $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d/m/Y') : '',
                                    $jenisKelamin, $siswa->alamat, $siswa->nama_ayah, $siswa->nama_ibu,
                                    $siswa->telepon, $siswa->email, $siswa->kelas->nama ?? '',
                                    $siswa->kelas->tingkat->nama ?? '', $siswa->kelas->tahun->nama ?? '', $status,
                                ];
                            }

                            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
                            {
                                return [
                                    1 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE2E2E2']]],
                                ];
                            }
                        }, 'data-siswa-selected-' . now()->format('Y-m-d') . '.xlsx');
                    })
                    ->deselectRecordsAfterCompletion(),
                DeleteBulkAction::make()
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportAll')
                    ->label('Export All to Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        return Excel::download(new SiswaExport(), 'data-siswa-semua-' . now()->format('Y-m-d') . '.xlsx');
                    }),
                Tables\Actions\Action::make('exportFiltered')
                    ->label('Export with Filters')
                    ->icon('heroicon-o-funnel')
                    ->color('info')
                    ->form([
                        Select::make('kelas_id')
                            ->label('Filter by Kelas')
                            ->relationship('kelas', 'nama')
                            ->searchable()
                            ->preload(),
                        Select::make('status')
                            ->label('Filter by Status')
                            ->options([
                                '1' => 'Aktif',
                                '2' => 'Baru',
                                '3' => 'Pindahan',
                                '4' => 'Keluar',
                                '5' => 'Lulus',
                            ]),
                    ])
                    ->action(function (array $data) {
                        $filters = array_filter($data);
                        return Excel::download(new SiswaExport($filters), 'data-siswa-filtered-' . now()->format('Y-m-d') . '.xlsx');
                    }),
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

