<?php

namespace App\Filament\Widgets;

use App\Models\Pembayaran;
use App\Models\PembayaranLain;
use App\Models\PemasukanPengeluaranYayasan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TrendSaldoWidget extends ChartWidget
{
    protected static ?string $heading = 'Trend Saldo Yayasan (12 Bulan Terakhir)';
    protected static ?int $sort = 6;

    protected function getData(): array
    {
        $months = [];
        $saldoData = [];
        $runningSaldo = 0;

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;
            $monthName = $date->locale('id')->translatedFormat('M Y');

            $months[] = $monthName;

            // Calculate monthly changes
            $pembayaranSiswa = Pembayaran::whereMonth('tanggal_bayar', $month)
                ->whereYear('tanggal_bayar', $year)
                ->sum('jumlah_bayar');

            $pembayaranLain = PembayaranLain::whereMonth('tanggal_pembayaran', $month)
                ->whereYear('tanggal_pembayaran', $year)
                ->sum('jumlah');

            $pemasukanManual = PemasukanPengeluaranYayasan::where('jenis_transaksi', 'pemasukan')
                ->whereMonth('tanggal_transaksi', $month)
                ->whereYear('tanggal_transaksi', $year)
                ->sum('jumlah');

            $pengeluaranManual = PemasukanPengeluaranYayasan::where('jenis_transaksi', 'pengeluaran')
                ->whereMonth('tanggal_transaksi', $month)
                ->whereYear('tanggal_transaksi', $year)
                ->sum('jumlah');

            $monthlyChange = $pembayaranSiswa + $pembayaranLain + $pemasukanManual - $pengeluaranManual;
            $runningSaldo += $monthlyChange;

            $saldoData[] = $runningSaldo;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Saldo Yayasan',
                    'data' => $saldoData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => '#3B82F6',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
