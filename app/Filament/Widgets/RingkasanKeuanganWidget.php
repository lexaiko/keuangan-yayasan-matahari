<?php

namespace App\Filament\Widgets;

use App\Models\SaldoYayasan;
use App\Models\Pembayaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class RingkasanKeuanganWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // Saldo Yayasan
        $saldoYayasan = SaldoYayasan::getSaldoTerkini();

        // Pendapatan bulan ini
        $pendapatanBulanIni = Pembayaran::whereMonth('tanggal_bayar', $bulanIni)
            ->whereYear('tanggal_bayar', $tahunIni)
            ->sum('jumlah_bayar');

        return [
            Stat::make('Saldo Yayasan', 'Rp ' . number_format($saldoYayasan, 0, ',', '.'))
                ->description($saldoYayasan >= 0 ? 'Surplus' : 'Defisit')
                ->descriptionIcon($saldoYayasan >= 0 ? 'heroicon-m-building-office-2' : 'heroicon-m-exclamation-triangle')
                ->color($saldoYayasan >= 0 ? 'success' : 'danger')
                ->chart([
                    $saldoYayasan * 0.7,
                    $saldoYayasan * 0.8,
                    $saldoYayasan * 0.9,
                    $saldoYayasan,
                    $saldoYayasan
                ]),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'))
                ->description('Dari pembayaran siswa')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart([
                    $pendapatanBulanIni * 0.6,
                    $pendapatanBulanIni * 0.7,
                    $pendapatanBulanIni * 0.8,
                    $pendapatanBulanIni * 0.9,
                    $pendapatanBulanIni
                ]),
        ];
    }
}
