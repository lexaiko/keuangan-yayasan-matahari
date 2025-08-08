<?php

namespace App\Filament\Widgets;

use App\Models\SaldoYayasan;
use App\Models\SaldoKoperasi;
use App\Models\Pembayaran;
use App\Models\GajiPegawai;
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

        // Saldo Koperasi
        $saldoKoperasi = SaldoKoperasi::getSaldo();

        // Total Kas (Yayasan + Koperasi)
        $totalKas = $saldoYayasan + $saldoKoperasi;

        // Pendapatan bulan ini
        $pendapatanBulanIni = Pembayaran::whereMonth('tanggal_bayar', $bulanIni)
            ->whereYear('tanggal_bayar', $tahunIni)
            ->sum('jumlah_bayar');

        // Pengeluaran gaji bulan ini
        $pengeluaranGajiBulanIni = GajiPegawai::where('bulan', Carbon::now()->locale('id')->translatedFormat('F'))
            ->where('tahun', $tahunIni)
            ->where('status', 'dibayar')
            ->sum('total_gaji');

        return [
            Stat::make('Total Kas', 'Rp ' . number_format($totalKas, 0, ',', '.'))
                ->description('Yayasan + Koperasi')
                ->descriptionIcon($totalKas >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($totalKas >= 0 ? 'success' : 'danger')
                ->chart([
                    $saldoYayasan * 0.7,
                    $saldoYayasan * 0.8,
                    $saldoYayasan * 0.9,
                    $saldoYayasan,
                    $totalKas
                ]),

            Stat::make('Saldo Yayasan', 'Rp ' . number_format($saldoYayasan, 0, ',', '.'))
                ->description($saldoYayasan >= 0 ? 'Surplus' : 'Defisit')
                ->descriptionIcon($saldoYayasan >= 0 ? 'heroicon-m-building-office-2' : 'heroicon-m-exclamation-triangle')
                ->color($saldoYayasan >= 0 ? 'success' : 'danger'),

            Stat::make('Saldo Koperasi', 'Rp ' . number_format($saldoKoperasi, 0, ',', '.'))
                ->description('Dana koperasi pegawai')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($saldoKoperasi >= 0 ? 'info' : 'warning'),

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

            Stat::make('Pengeluaran Gaji', 'Rp ' . number_format($pengeluaranGajiBulanIni, 0, ',', '.'))
                ->description('Gaji pegawai bulan ini')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning')
                ->chart([
                    $pengeluaranGajiBulanIni * 0.8,
                    $pengeluaranGajiBulanIni * 0.85,
                    $pengeluaranGajiBulanIni * 0.9,
                    $pengeluaranGajiBulanIni * 0.95,
                    $pengeluaranGajiBulanIni
                ]),

            Stat::make('Net Flow Bulan Ini', 'Rp ' . number_format($pendapatanBulanIni - $pengeluaranGajiBulanIni, 0, ',', '.'))
                ->description(($pendapatanBulanIni - $pengeluaranGajiBulanIni) >= 0 ? 'Surplus bulan ini' : 'Defisit bulan ini')
                ->descriptionIcon(($pendapatanBulanIni - $pengeluaranGajiBulanIni) >= 0 ? 'heroicon-m-arrow-up' : 'heroicon-m-arrow-down')
                ->color(($pendapatanBulanIni - $pengeluaranGajiBulanIni) >= 0 ? 'success' : 'danger'),
        ];
    }
}
