<?php

namespace App\Filament\Widgets;

use App\Models\SaldoYayasan;
use App\Models\Pembayaran;
use App\Models\PembayaranLain;
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

        // Saldo Yayasan dengan formula baru
        $saldoYayasan = SaldoYayasan::getSaldoTerkini();

        // Pendapatan bulan ini (pembayaran siswa + pembayaran lain-lain)
        $pembayaranSiswaBulanIni = Pembayaran::whereMonth('tanggal_bayar', $bulanIni)
            ->whereYear('tanggal_bayar', $tahunIni)
            ->sum('jumlah_bayar');

        $pembayaranLainBulanIni = PembayaranLain::whereMonth('tanggal_pembayaran', $bulanIni)
            ->whereYear('tanggal_pembayaran', $tahunIni)
            ->sum('jumlah');

        $totalPendapatanBulanIni = $pembayaranSiswaBulanIni + $pembayaranLainBulanIni;

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

            Stat::make('Total Pendapatan Bulan Ini', 'Rp ' . number_format($totalPendapatanBulanIni, 0, ',', '.'))
                ->description("Siswa + Lain-lain")
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart([
                    $totalPendapatanBulanIni * 0.6,
                    $totalPendapatanBulanIni * 0.7,
                    $totalPendapatanBulanIni * 0.8,
                    $totalPendapatanBulanIni * 0.9,
                    $totalPendapatanBulanIni
                ]),
        ];
    }
}
