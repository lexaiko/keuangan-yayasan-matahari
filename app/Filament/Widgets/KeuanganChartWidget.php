<?php

namespace App\Filament\Widgets;

use App\Models\SaldoYayasan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class KeuanganChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Trend Keuangan Yayasan (6 Bulan Terakhir)';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $months = [];
        $pendapatanData = [];
        $pengeluaranData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;
            $monthName = $date->locale('id')->translatedFormat('M Y');

            $months[] = $monthName;

            // Pendapatan dari saldo yayasan
            $pendapatan = SaldoYayasan::where('jenis_transaksi', 'pendapatan')
                ->whereMonth('tanggal_transaksi', $month)
                ->whereYear('tanggal_transaksi', $year)
                ->sum('jumlah');

            // Pengeluaran dari saldo yayasan
            $pengeluaran = SaldoYayasan::where('jenis_transaksi', 'pengeluaran')
                ->whereMonth('tanggal_transaksi', $month)
                ->whereYear('tanggal_transaksi', $year)
                ->sum('jumlah');

            $pendapatanData[] = $pendapatan;
            $pengeluaranData[] = $pengeluaran;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $pendapatanData,
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#059669',
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $pengeluaranData,
                    'backgroundColor' => '#EF4444',
                    'borderColor' => '#DC2626',
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

