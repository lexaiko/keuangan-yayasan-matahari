<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use App\Models\Pembayaran;
use App\Models\PembayaranLain;
use App\Models\PemasukanPengeluaranYayasan;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\CursorPaginator;

class LaporanKeuanganPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string $view = 'filament.pages.laporan-keuangan-page';
    protected static ?string $navigationLabel = 'Laporan Keuangan';
    protected static ?string $title = 'Laporan Keuangan';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];
    public $dari_tanggal;
    public $sampai_tanggal;
    public $jenis_laporan = 'semua';

    public function mount(): void
    {
        $this->dari_tanggal = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->sampai_tanggal = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->form->fill([
            'dari_tanggal' => $this->dari_tanggal,
            'sampai_tanggal' => $this->sampai_tanggal,
            'jenis_laporan' => $this->jenis_laporan,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('dari_tanggal')
                    ->label('Dari Tanggal')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state) => $this->dari_tanggal = $state),

                DatePicker::make('sampai_tanggal')
                    ->label('Sampai Tanggal')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state) => $this->sampai_tanggal = $state),

                Select::make('jenis_laporan')
                    ->label('Jenis Laporan')
                    ->options([
                        'semua' => 'Semua Transaksi',
                        'pemasukan' => 'Hanya Pemasukan',
                        'pengeluaran' => 'Hanya Pengeluaran',
                    ])
                    ->live()
                    ->afterStateUpdated(fn ($state) => $this->jenis_laporan = $state),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                // We'll override getTableRecords, so return empty query
                return \App\Models\Pembayaran::query()->whereRaw('1 = 0');
            })
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->alignCenter()
                    ->width('60px'),

                TextColumn::make('dari')
                    ->label('Dari')
                    ->searchable()
                    ->wrap()
                    ->weight('medium'),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->alignCenter()
                    ->width('100px'),

                TextColumn::make('debit')
                    ->label('Debit')
                    ->alignEnd()
                    ->width('130px')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'Rp ' . number_format($state, 0, ',', '.') : '-')
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),

                TextColumn::make('kredit')
                    ->label('Kredit')
                    ->alignEnd()
                    ->width('130px')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'Rp ' . number_format($state, 0, ',', '.') : '-')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray'),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->wrap()
                    ->limit(50),
            ])
            ->defaultSort('tanggal', 'desc')
            ->striped()
            ->paginated(false);
    }

    public function getTableRecords(): EloquentCollection|Paginator|CursorPaginator
    {
        // Convert Collection to EloquentCollection for compatibility
        $data = $this->getFinancialData();

        // Create a new EloquentCollection with our data
        return new EloquentCollection($data->toArray());
    }

    public function getFinancialData(): Collection
    {
        $query = collect();
        $no = 1;

        // Pembayaran Siswa (Debit/Pemasukan)
        if ($this->jenis_laporan === 'semua' || $this->jenis_laporan === 'pemasukan') {
            $pembayaranSiswa = Pembayaran::with('siswa')
                ->whereBetween('tanggal_bayar', [$this->dari_tanggal, $this->sampai_tanggal])
                ->get()
                ->map(function ($item) use (&$no) {
                    return (object) [
                        'no' => $no++,
                        'dari' => $item->siswa->nama ?? 'Siswa tidak ditemukan',
                        'tanggal' => $item->tanggal_bayar,
                        'debit' => $item->jumlah_bayar,
                        'kredit' => 0,
                        'keterangan' => "Pembayaran siswa - Invoice #{$item->id}",
                        'jenis' => 'pembayaran_siswa',
                        'referensi' => "Invoice #{$item->id}",
                    ];
                });
            $query = $query->merge($pembayaranSiswa);
        }

        // Pembayaran Lain-lain (Debit/Pemasukan)
        if ($this->jenis_laporan === 'semua' || $this->jenis_laporan === 'pemasukan') {
            $pembayaranLain = PembayaranLain::with('jenisPembayaranLain')
                ->whereBetween('tanggal_pembayaran', [$this->dari_tanggal, $this->sampai_tanggal])
                ->get()
                ->map(function ($item) use (&$no) {
                    return (object) [
                        'no' => $no++,
                        'dari' => $item->nama_pembayar,
                        'tanggal' => $item->tanggal_pembayaran,
                        'debit' => $item->jumlah,
                        'kredit' => 0,
                        'keterangan' => "Pembayaran lain-lain: {$item->jenisPembayaranLain->nama_jenis}",
                        'jenis' => 'pembayaran_lain',
                        'referensi' => "PL-{$item->id}",
                    ];
                });
            $query = $query->merge($pembayaranLain);
        }

        // Pemasukan Manual (Debit/Pemasukan)
        if ($this->jenis_laporan === 'semua' || $this->jenis_laporan === 'pemasukan') {
            $pemasukanManual = PemasukanPengeluaranYayasan::with('kategoriPemasukanPengeluaran')
                ->where('jenis_transaksi', 'pemasukan')
                ->whereBetween('tanggal_transaksi', [$this->dari_tanggal, $this->sampai_tanggal])
                ->get()
                ->map(function ($item) use (&$no) {
                    return (object) [
                        'no' => $no++,
                        'dari' => 'Pemasukan Manual',
                        'tanggal' => $item->tanggal_transaksi,
                        'debit' => $item->jumlah,
                        'kredit' => 0,
                        'keterangan' => $item->keterangan ?: "Kategori: {$item->kategoriPemasukanPengeluaran->nama_kategori}",
                        'jenis' => 'pemasukan_manual',
                        'referensi' => "PM-{$item->id}",
                    ];
                });
            $query = $query->merge($pemasukanManual);
        }

        // Pengeluaran Manual (Kredit/Pengeluaran)
        if ($this->jenis_laporan === 'semua' || $this->jenis_laporan === 'pengeluaran') {
            $pengeluaranManual = PemasukanPengeluaranYayasan::with('kategoriPemasukanPengeluaran')
                ->where('jenis_transaksi', 'pengeluaran')
                ->whereBetween('tanggal_transaksi', [$this->dari_tanggal, $this->sampai_tanggal])
                ->get()
                ->map(function ($item) use (&$no) {
                    return (object) [
                        'no' => $no++,
                        'dari' => 'Pengeluaran Manual',
                        'tanggal' => $item->tanggal_transaksi,
                        'debit' => 0,
                        'kredit' => $item->jumlah,
                        'keterangan' => $item->keterangan ?: "Kategori: {$item->kategoriPemasukanPengeluaran->nama_kategori}",
                        'jenis' => 'pengeluaran_manual',
                        'referensi' => "PK-{$item->id}",
                    ];
                });
            $query = $query->merge($pengeluaranManual);
        }

        // Sort by date and re-number
        $sortedData = $query->sortByDesc('tanggal')->values();

        // Re-assign sequential numbers
        $sortedData = $sortedData->map(function ($item, $index) {
            $item->no = $index + 1;
            return $item;
        });

        return $sortedData;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action('exportToPdf'),
        ];
    }

    public function exportToPdf()
    {
        $data = $this->getFinancialData();

        $totalDebit = $data->sum('debit');
        $totalKredit = $data->sum('kredit');
        $saldoAkhir = $totalDebit - $totalKredit;

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.laporan-keuangan', [
            'data' => $data,
            'dari_tanggal' => $this->dari_tanggal,
            'sampai_tanggal' => $this->sampai_tanggal,
            'jenis_laporan' => $this->jenis_laporan,
            'total_debit' => $totalDebit,
            'total_kredit' => $totalKredit,
            'saldo_akhir' => $saldoAkhir,
        ]);

        $filename = 'laporan-keuangan-' . Carbon::parse($this->dari_tanggal)->format('Y-m-d') . '-sampai-' . Carbon::parse($this->sampai_tanggal)->format('Y-m-d') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function getTotalDebit()
    {
        return $this->getFinancialData()->sum('debit');
    }

    public function getTotalKredit()
    {
        return $this->getFinancialData()->sum('kredit');
    }

    public function getSaldoAkhir()
    {
        return $this->getTotalDebit() - $this->getTotalKredit();
    }
}
