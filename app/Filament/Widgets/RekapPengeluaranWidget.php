<?php

namespace App\Filament\Widgets;

use App\Models\PemasukanPengeluaranYayasan;
use App\Models\KategoriPemasukanPengeluaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class RekapPengeluaranWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // Total pengeluaran bulan ini
        $totalPengeluaranBulanIni = PemasukanPengeluaranYayasan::where('jenis_transaksi', 'pengeluaran')
            ->whereMonth('tanggal_transaksi', $bulanIni)
            ->whereYear('tanggal_transaksi', $tahunIni)
            ->sum('jumlah');

        // Top 2 kategori pengeluaran bulan ini
        $topKategori = PemasukanPengeluaranYayasan::where('jenis_transaksi', 'pengeluaran')
            ->whereMonth('tanggal_transaksi', $bulanIni)
            ->whereYear('tanggal_transaksi', $tahunIni)
            ->join('kategori_pemasukan_pengeluaran', 'pemasukan_pengeluaran_yayasan.kategori_id', '=', 'kategori_pemasukan_pengeluaran.id')
            ->selectRaw('kategori_pemasukan_pengeluaran.nama_kategori, SUM(pemasukan_pengeluaran_yayasan.jumlah) as total')
            ->groupBy('kategori_pemasukan_pengeluaran.id', 'kategori_pemasukan_pengeluaran.nama_kategori')
            ->orderBy('total', 'desc')
            ->limit(2)
            ->get();

        // Total pengeluaran all time
        $totalPengeluaranKeseluruhan = PemasukanPengeluaranYayasan::where('jenis_transaksi', 'pengeluaran')
            ->sum('jumlah');

        $kategori1 = $topKategori->first();
        $kategori2 = $topKategori->skip(1)->first();

        return [
            Stat::make('Total Pengeluaran Bulan Ini', 'Rp ' . number_format($totalPengeluaranBulanIni, 0, ',', '.'))
                ->description('Total keseluruhan: Rp ' . number_format($totalPengeluaranKeseluruhan, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([
                    $totalPengeluaranBulanIni * 0.7,
                    $totalPengeluaranBulanIni * 0.85,
                    $totalPengeluaranBulanIni * 0.95,
                    $totalPengeluaranBulanIni
                ]),

            Stat::make($kategori1 ? $kategori1->nama_kategori : 'Kategori Terbesar',
                      $kategori1 ? 'Rp ' . number_format($kategori1->total, 0, ',', '.') : 'Rp 0')
                ->description('Pengeluaran terbesar bulan ini')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make($kategori2 ? $kategori2->nama_kategori : 'Kategori Kedua',
                      $kategori2 ? 'Rp ' . number_format($kategori2->total, 0, ',', '.') : 'Rp 0')
                ->description('Pengeluaran terbesar kedua')
                ->descriptionIcon('heroicon-m-information-circle')
                ->color('gray'),
        ];
    }
}
