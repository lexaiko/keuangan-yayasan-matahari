<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\SaldoYayasanWidget;
use App\Filament\Widgets\KeuanganChartWidget;
use App\Filament\Widgets\SaldoKoperasiWidget;
use App\Filament\Widgets\TransaksiTerakhirWidget;
use App\Filament\Widgets\RekapPendapatanWidget;
use App\Filament\Widgets\RekapPengeluaranWidget;
use App\Filament\Widgets\SaldoBreakdownWidget;
use App\Filament\Resources\AdminResource\Widgets\FilterSaldoKoperasiWidget;

class KeuanganDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static string $view = 'filament.pages.keuangan-dashboard';
    protected static ?string $navigationLabel = 'Dashboard Keuangan';
    protected static ?string $title = 'Dashboard Keuangan';
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?int $navigationSort = -21;

    protected function getHeaderWidgets(): array
    {
        return [
            // RingkasanKeuanganWidget::class,
            SaldoYayasanWidget::class,
            RekapPendapatanWidget::class,
            RekapPengeluaranWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            KeuanganChartWidget::class,
            SaldoBreakdownWidget::class,
        ];
    }

    public function getTitle(): string
    {
        return "Dashboard Keuangan - " . now()->format('F Y');
    }
}
