<?php

namespace App\Filament\Resources\AdminResource\Widgets;

use App\Models\SaldoKoperasi;
use Filament\Widgets\ChartWidget;


class SaldoBatangWidget extends ChartWidget
{

    protected int|string|array $columnSpan = '1'; // bar chart satu baris penuh

    protected static ?string $heading = 'Perbandingan Saldo Masuk & Keluar';

    protected function getData(): array
    {
        $summary = SaldoKoperasi::getSummaryMasukKeluar();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Saldo',
                    'data' => array_values($summary),
                    'backgroundColor' => ['#22c55e', '#ef4444'], // Hijau & Merah
                ],
            ],
            'labels' => array_keys($summary),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // bar chart
    }
}
