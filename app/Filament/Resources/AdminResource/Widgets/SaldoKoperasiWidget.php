<?php

namespace App\Filament\Resources\AdminResource\Widgets;
use App\Models\SaldoKoperasi;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class SaldoKoperasiWidget extends BaseWidget
{

    protected function getCards(): array
    {
        $saldo = SaldoKoperasi::getSaldo();

        return [
            Card::make('Saldo Koperasi', 'Rp ' . number_format($saldo, 0, ',', '.'))
                ->description('Saldo saat ini')
                ->icon('heroicon-o-banknotes')
                ->color($saldo >= 0 ? 'success' : 'danger'),
        ];
    }
}
