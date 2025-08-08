<?php

namespace App\Filament\Widgets;

use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\JenisPembayaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class SppBelumBayarWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $bulanIni = Carbon::now()->locale('id')->translatedFormat('F');
        $tahunIni = Carbon::now()->year;

        // Get SPP payment type
        $sppJenis = JenisPembayaran::where('nama_pembayaran', 'LIKE', '%SPP%')
            ->orWhere('nama_pembayaran', 'LIKE', '%spp%')
            ->first();

        $totalSiswaAktif = Siswa::where('status', '1')->count();

        $siswaLunasSpp = 0;
        $totalTagihanSpp = 0;

        if ($sppJenis) {
            // Count total SPP bills for this month
            $totalTagihanSpp = Tagihan::where('jenis_pembayaran_id', $sppJenis->id)
                ->where('bulan', $bulanIni)
                ->whereHas('siswa', function($query) {
                    $query->where('status', '1');
                })
                ->count();

            // Count students who have paid SPP this month (status lunas)
            $siswaLunasSpp = Tagihan::where('jenis_pembayaran_id', $sppJenis->id)
                ->where('bulan', $bulanIni)
                ->where('status', Tagihan::STATUS_LUNAS)
                ->whereHas('siswa', function($query) {
                    $query->where('status', '1');
                })
                ->count();
        }

        $sppBelumBayar = $totalTagihanSpp - $siswaLunasSpp;
        $persentaseLunas = $totalTagihanSpp > 0 ? round(($siswaLunasSpp / $totalTagihanSpp) * 100, 1) : 0;

        return [
            Stat::make('SPP Belum Bayar Bulan Ini', $sppBelumBayar)
                ->description("Dari {$totalTagihanSpp} tagihan SPP {$bulanIni}")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($sppBelumBayar > 0 ? 'danger' : 'success')
                ->chart([7, 12, 8, 15, 10, 6, $sppBelumBayar]),

            Stat::make('SPP Sudah Lunas', $siswaLunasSpp)
                ->description("Persentase: {$persentaseLunas}%")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([3, 8, 12, 15, 18, 20, $siswaLunasSpp]),

            Stat::make('Total Siswa Aktif', $totalSiswaAktif)
                ->description('Siswa dengan status aktif')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->chart([45, 48, 50, 52, 48, 50, $totalSiswaAktif]),
        ];
    }
}
