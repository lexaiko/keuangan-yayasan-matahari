<?php

namespace App\Filament\Widgets;

use App\Models\SaldoYayasan;
use App\Models\GajiPegawai;
use App\Models\Pembayaran;
use App\Models\PembayaranLain;
use App\Models\PemasukanPengeluaranYayasan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class SaldoYayasanWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // Saldo terkini dengan formula baru: (Pemasukan - Pengeluaran) + Pembayaran Siswa + Pembayaran Lain-Lain
        $saldoTerkini = SaldoYayasan::getSaldoTerkini();

        // Pendapatan bulan ini (pembayaran siswa + pembayaran lain-lain)
        $pembayaranSiswaBulanIni = Pembayaran::whereMonth('tanggal_bayar', $bulanIni)
            ->whereYear('tanggal_bayar', $tahunIni)
            ->sum('jumlah_bayar');

        $pembayaranLainBulanIni = PembayaranLain::whereMonth('tanggal_pembayaran', $bulanIni)
            ->whereYear('tanggal_pembayaran', $tahunIni)
            ->sum('jumlah');

        $totalPendapatanBulanIni = $pembayaranSiswaBulanIni + $pembayaranLainBulanIni;

        // Pengeluaran bulan ini
        $pengeluaranBulanIni = PemasukanPengeluaranYayasan::where('jenis_transaksi', 'pengeluaran')
            ->whereMonth('tanggal_transaksi', $bulanIni)
            ->whereYear('tanggal_transaksi', $tahunIni)
            ->sum('jumlah');

        return [
            Stat::make('Saldo Yayasan', 'Rp ' . number_format($saldoTerkini, 0, ',', '.'))
                ->description($saldoTerkini >= 0 ? 'Saldo positif' : 'Saldo minus (defisit)')
                ->descriptionIcon($saldoTerkini >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($saldoTerkini >= 0 ? 'success' : 'danger'),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($totalPendapatanBulanIni, 0, ',', '.'))
                ->description("Siswa: Rp " . number_format($pembayaranSiswaBulanIni, 0, ',', '.') . " | Lain-lain: Rp " . number_format($pembayaranLainBulanIni, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([
                    $totalPendapatanBulanIni * 0.7,
                    $totalPendapatanBulanIni * 0.8,
                    $totalPendapatanBulanIni * 0.9,
                    $totalPendapatanBulanIni
                ]),

            Stat::make('Pengeluaran Bulan Ini', 'Rp ' . number_format($pengeluaranBulanIni, 0, ',', '.'))
                ->description("Total pengeluaran operasional")
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
