<?php

namespace App\Filament\Widgets;

use App\Models\Pembayaran;
use App\Models\PembayaranLain;
use App\Models\PemasukanPengeluaranYayasan;
use Filament\Widgets\ChartWidget;

class SaldoBreakdownWidget extends ChartWidget
{
    protected static ?string $heading = 'Komposisi Saldo Yayasan';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        // Total pembayaran siswa
        $pembayaranSiswa = Pembayaran::sum('jumlah_bayar');

        // Total pembayaran lain-lain
        $pembayaranLain = PembayaranLain::sum('jumlah');

        // Net pemasukan manual (pemasukan - pengeluaran)
        $pemasukanManual = PemasukanPengeluaranYayasan::where('jenis_transaksi', 'pemasukan')->sum('jumlah');
        $pengeluaranManual = PemasukanPengeluaranYayasan::where('jenis_transaksi', 'pengeluaran')->sum('jumlah');
        $netManual = $pemasukanManual - $pengeluaranManual;

        return [
            'datasets' => [
                [
                    'data' => [$pembayaranSiswa, $pembayaranLain, $netManual],
                    'backgroundColor' => [
                        '#10B981', // Green for student payments
                        '#3B82F6', // Blue for other payments
                        $netManual >= 0 ? '#F59E0B' : '#EF4444', // Amber if positive, Red if negative
                    ],
                    'borderColor' => [
                        '#059669',
                        '#2563EB',
                        $netManual >= 0 ? '#D97706' : '#DC2626',
                    ],
                ],
            ],
            'labels' => [
                'Pembayaran Siswa (Rp ' . number_format($pembayaranSiswa, 0, ',', '.') . ')',
                'Pembayaran Lain-lain (Rp ' . number_format($pembayaranLain, 0, ',', '.') . ')',
                'Transaksi Yayasan (Rp ' . number_format($netManual, 0, ',', '.') . ')',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
