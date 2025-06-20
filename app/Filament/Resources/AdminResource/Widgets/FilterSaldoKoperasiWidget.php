<?php

namespace App\Filament\Resources\AdminResource\Widgets;

use ArberMustafa\FilamentGoogleCharts\Widgets\PieChartWidget;
use Illuminate\Support\Facades\DB;

class FilterSaldoKoperasiWidget extends PieChartWidget
{
    protected static ?string $heading = 'Komposisi Saldo Tabungan Pegawai';
    protected int|string|array $columnSpan = '1';

    protected static ?array $options = [
        'legend' => ['position' => 'right'],
        'height' => 280,
    ];

    protected function getData(): array
    {
        $rows = DB::table('saldo_koperasis')
            ->join('users', 'users.id', '=', 'saldo_koperasis.pelaku_terkait_id')
            ->select('users.name')
            ->selectRaw('SUM(CASE WHEN tipe = "masuk" THEN jumlah ELSE -jumlah END) as saldo')
            ->where('kategori', 'tabungan')
            ->groupBy('users.name')
            ->get();

        $data = [['Pegawai', 'Saldo']];
        foreach ($rows as $row) {
            $data[] = [$row->name, (float) $row->saldo];
        }

        return $data;
    }
}
