<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\SppBelumBayarWidget;
use App\Filament\Widgets\DetailSppBelumBayarWidget;
use App\Filament\Widgets\SaldoYayasanWidget;
use App\Filament\Widgets\KeuanganChartWidget;

class SiswaDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static string $view = 'filament.pages.siswa-dashboard';
    protected static ?string $navigationLabel = 'Dashboard Siswa';
    protected static ?string $title = 'Dashboard Siswa';
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?int $navigationSort = -20;

    protected function getHeaderWidgets(): array
    {
        return [
            SppBelumBayarWidget::class,
            SaldoYayasanWidget::class,
            \App\Filament\Widgets\SppTrendWidget::class,
            KeuanganChartWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            DetailSppBelumBayarWidget::class,
        ];
    }

    public function getTitle(): string
    {
        $bulanIni = \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y');
        return "Dashboard Siswa & Keuangan - {$bulanIni}";
    }
}
