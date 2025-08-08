<?php

namespace App\Filament\Widgets;

use App\Models\SaldoYayasan;
use App\Models\GajiPegawai;
use App\Models\Pembayaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class SaldoYayasanWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // Saldo terkini (otomatis dari semua transaksi) - bisa minus
        $saldoTerkini = SaldoYayasan::getSaldoTerkini();

        // Pendapatan bulan ini (dari saldo yayasan kategori pembayaran siswa)
        $pendapatanBulanIni = SaldoYayasan::where('jenis_transaksi', 'pendapatan')
            ->where('kategori', 'Pembayaran Siswa')
            ->whereMonth('tanggal_transaksi', $bulanIni)
            ->whereYear('tanggal_transaksi', $tahunIni)
            ->sum('jumlah');

        // Pengeluaran bulan ini (total semua pengeluaran)
        $pengeluaranBulanIni = SaldoYayasan::where('jenis_transaksi', 'pengeluaran')
            ->whereMonth('tanggal_transaksi', $bulanIni)
            ->whereYear('tanggal_transaksi', $tahunIni)
            ->sum('jumlah');

        // Breakdown pengeluaran gaji
        $pengeluaranGajiBulanIni = SaldoYayasan::where('jenis_transaksi', 'pengeluaran')
            ->where('kategori', 'Gaji Pegawai')
            ->whereMonth('tanggal_transaksi', $bulanIni)
            ->whereYear('tanggal_transaksi', $tahunIni)
            ->sum('jumlah');

        return [
            Stat::make('Saldo Yayasan', 'Rp ' . number_format($saldoTerkini, 0, ',', '.'))
                ->description($saldoTerkini >= 0 ? 'Saldo positif' : 'Saldo minus (defisit)')
                ->descriptionIcon($saldoTerkini >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($saldoTerkini >= 0 ? 'success' : 'danger'),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'))
                ->description('Dari pembayaran siswa')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([
                    $pendapatanBulanIni * 0.7,
                    $pendapatanBulanIni * 0.8,
                    $pendapatanBulanIni * 0.9,
                    $pendapatanBulanIni
                ]),

            Stat::make('Pengeluaran Bulan Ini', 'Rp ' . number_format($pengeluaranBulanIni, 0, ',', '.'))
                ->description("Gaji: Rp " . number_format($pengeluaranGajiBulanIni, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning')
                ->chart([
                    $pengeluaranBulanIni * 0.8,
                    $pengeluaranBulanIni * 0.9,
                    $pengeluaranBulanIni * 0.95,
                    $pengeluaranBulanIni
                ]),
        ];
    }
}
