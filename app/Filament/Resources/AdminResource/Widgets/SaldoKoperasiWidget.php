<?php

namespace App\Filament\Resources\AdminResource\Widgets;

use App\Models\SaldoKoperasi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SaldoKoperasiWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $saldo = SaldoKoperasi::getSaldo();
        $summary = SaldoKoperasi::getSummaryMasukKeluar();

        return [
            Stat::make('Saldo Koperasi', 'Rp ' . number_format($saldo, 0, ',', '.'))
                ->description('Saldo saat ini')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color($saldo >= 0 ? 'success' : 'danger'),

            Stat::make('Total Dana Masuk', 'Rp ' . number_format($summary['Masuk'], 0, ',', '.'))
                ->description('Sejak awal operasi')
                ->descriptionIcon('heroicon-o-arrow-down-tray')
                ->color('success'),

            Stat::make('Total Dana Keluar', 'Rp ' . number_format($summary['Keluar'], 0, ',', '.'))
                ->description('Sejak awal operasi')
                ->descriptionIcon('heroicon-o-arrow-up-tray')
                ->color('warning'),
        ];
    }
}
