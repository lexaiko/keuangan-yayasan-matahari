<?php

namespace App\Filament\Widgets;

use App\Models\Tagihan;
use App\Models\JenisPembayaran;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SppTrendWidget extends ChartWidget
{
    protected static ?string $heading = 'Trend Pembayaran SPP 6 Bulan Terakhir';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Get SPP payment type
        $sppJenis = JenisPembayaran::where('nama_pembayaran', 'LIKE', '%SPP%')
            ->orWhere('nama_pembayaran', 'LIKE', '%spp%')
            ->first();

        if (!$sppJenis) {
            return [
                'datasets' => [
                    [
                        'label' => 'SPP Lunas',
                        'data' => [0, 0, 0, 0, 0, 0],
                        'backgroundColor' => '#10B981',
                    ],
                    [
                        'label' => 'SPP Belum Bayar',
                        'data' => [0, 0, 0, 0, 0, 0],
                        'backgroundColor' => '#EF4444',
                    ],
                ],
                'labels' => ['6 bulan lalu', '5 bulan lalu', '4 bulan lalu', '3 bulan lalu', '2 bulan lalu', 'Bulan ini'],
            ];
        }

        $months = [];
        $lunasData = [];
        $belumBayarData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $bulan = $date->locale('id')->translatedFormat('F');
            $months[] = $bulan;

            $lunas = Tagihan::where('jenis_pembayaran_id', $sppJenis->id)
                ->where('bulan', $bulan)
                ->where('status', Tagihan::STATUS_LUNAS)
                ->whereHas('siswa', function($query) {
                    $query->where('status', '1');
                })
                ->count();

            $belumBayar = Tagihan::where('jenis_pembayaran_id', $sppJenis->id)
                ->where('bulan', $bulan)
                ->where('status', '!=', Tagihan::STATUS_LUNAS)
                ->whereHas('siswa', function($query) {
                    $query->where('status', '1');
                })
                ->count();

            $lunasData[] = $lunas;
            $belumBayarData[] = $belumBayar;
        }

        return [
            'datasets' => [
                [
                    'label' => 'SPP Lunas',
                    'data' => $lunasData,
                    'backgroundColor' => '#10B981',
                ],
                [
                    'label' => 'SPP Belum Bayar',
                    'data' => $belumBayarData,
                    'backgroundColor' => '#EF4444',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
