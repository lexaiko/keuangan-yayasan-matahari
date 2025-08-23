<?php

namespace App\Filament\Widgets;

use App\Models\Pembayaran;
use App\Models\PembayaranLain;
use App\Models\PemasukanPengeluaranYayasan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class RekapPendapatanWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // Pembayaran siswa bulan ini
        $pembayaranSiswa = Pembayaran::whereMonth('tanggal_bayar', $bulanIni)
            ->whereYear('tanggal_bayar', $tahunIni)
            ->sum('jumlah_bayar');

        // Pembayaran lain-lain bulan ini
        $pembayaranLain = PembayaranLain::whereMonth('tanggal_pembayaran', $bulanIni)
            ->whereYear('tanggal_pembayaran', $tahunIni)
            ->sum('jumlah');

        // Pemasukan manual bulan ini
        $pemasukanManual = PemasukanPengeluaranYayasan::where('jenis_transaksi', 'pemasukan')
            ->whereMonth('tanggal_transaksi', $bulanIni)
            ->whereYear('tanggal_transaksi', $tahunIni)
            ->sum('jumlah');

        // Total pembayaran siswa (all time)
        $totalPembayaranSiswa = Pembayaran::sum('jumlah_bayar');

        // Total pembayaran lain-lain (all time)
        $totalPembayaranLain = PembayaranLain::sum('jumlah');

        return [
            Stat::make('Pembayaran Siswa Bulan Ini', 'Rp ' . number_format($pembayaranSiswa, 0, ',', '.'))
                ->description('Total: Rp ' . number_format($totalPembayaranSiswa, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->chart([
                    $pembayaranSiswa * 0.6,
                    $pembayaranSiswa * 0.8,
                    $pembayaranSiswa * 0.9,
                    $pembayaranSiswa
                ]),

            Stat::make('Pembayaran Lain-Lain Bulan Ini', 'Rp ' . number_format($pembayaranLain, 0, ',', '.'))
                ->description('Total: Rp ' . number_format($totalPembayaranLain, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info')
                ->chart([
                    $pembayaranLain * 0.5,
                    $pembayaranLain * 0.7,
                    $pembayaranLain * 0.85,
                    $pembayaranLain
                ]),

            Stat::make('Pemasukan Manual Bulan Ini', 'Rp ' . number_format($pemasukanManual, 0, ',', '.'))
                ->description('Pemasukan operasional')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning')
                ->chart([
                    $pemasukanManual * 0.4,
                    $pemasukanManual * 0.6,
                    $pemasukanManual * 0.8,
                    $pemasukanManual
                ]),
        ];
    }
}
