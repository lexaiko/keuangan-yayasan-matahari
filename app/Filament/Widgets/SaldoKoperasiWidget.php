<?php

namespace App\Filament\Widgets;

use App\Models\SaldoKoperasi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class SaldoKoperasiWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // Total saldo koperasi
        $saldoTotal = SaldoKoperasi::getSaldo();

        // Summary masuk keluar
        $summary = SaldoKoperasi::getSummaryMasukKeluar();

        // Transaksi bulan ini
        $masukBulanIni = SaldoKoperasi::where('tipe', 'masuk')
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->sum('jumlah');

        $keluarBulanIni = SaldoKoperasi::where('tipe', 'keluar')
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->sum('jumlah');

        // Jumlah pegawai yang memiliki saldo
        $pegawaiMemilikiSaldo = SaldoKoperasi::getSaldoPerPegawai()->count();

        return [
            Stat::make('Saldo Koperasi', 'Rp ' . number_format($saldoTotal, 0, ',', '.'))
                ->description($saldoTotal >= 0 ? 'Dana tersedia' : 'Dana minus')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color($saldoTotal >= 0 ? 'success' : 'danger')
                ->chart([
                    $saldoTotal * 0.7,
                    $saldoTotal * 0.8,
                    $saldoTotal * 0.9,
                    $saldoTotal
                ]),

            Stat::make('Dana Masuk Bulan Ini', 'Rp ' . number_format($masukBulanIni, 0, ',', '.'))
                ->description('Tabungan & setoran')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('success'),

            Stat::make('Dana Keluar Bulan Ini', 'Rp ' . number_format($keluarBulanIni, 0, ',', '.'))
                ->description('Pinjaman & penarikan')
                ->descriptionIcon('heroicon-m-arrow-up-tray')
                ->color('warning'),

            Stat::make('Net Flow Koperasi', 'Rp ' . number_format($masukBulanIni - $keluarBulanIni, 0, ',', '.'))
                ->description(($masukBulanIni - $keluarBulanIni) >= 0 ? 'Surplus' : 'Defisit')
                ->descriptionIcon(($masukBulanIni - $keluarBulanIni) >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color(($masukBulanIni - $keluarBulanIni) >= 0 ? 'success' : 'danger'),

            Stat::make('Pegawai Aktif', $pegawaiMemilikiSaldo . ' pegawai')
                ->description('Memiliki saldo koperasi')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Total Historis Dana Masuk', 'Rp ' . number_format($summary['Masuk'], 0, ',', '.'))
                ->description('Sejak awal operasi')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }
}
