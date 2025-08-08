<?php

namespace App\Filament\Widgets;

use App\Models\Tagihan;
use App\Models\JenisPembayaran;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class DetailSppBelumBayarWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        $bulanIni = Carbon::now()->locale('id')->translatedFormat('F');

        // Get SPP payment type
        $sppJenis = JenisPembayaran::where('nama_pembayaran', 'LIKE', '%SPP%')
            ->orWhere('nama_pembayaran', 'LIKE', '%spp%')
            ->first();

        $query = Tagihan::query();

        if ($sppJenis) {
            $query->where('jenis_pembayaran_id', $sppJenis->id)
                ->where('bulan', $bulanIni)
                ->where('status', '!=', Tagihan::STATUS_LUNAS)
                ->whereHas('siswa', function($q) {
                    $q->where('status', '1');
                });
        } else {
            // If no SPP found, return empty query
            $query->whereRaw('1 = 0');
        }

        return $table
            ->query($query)
            ->heading("Daftar Siswa Belum Bayar SPP {$bulanIni}")
            ->description('Siswa yang belum melunasi pembayaran SPP bulan ini')
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('siswa.kelas.nama')
                    ->label('Kelas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bulan')
                    ->label('Bulan')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah Tagihan')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'lunas' => 'Lunas',
                        'belum_bayar' => 'Belum Bayar',
                        'sebagian' => 'Sebagian',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'lunas' => 'success',
                        'belum_bayar' => 'danger',
                        'sebagian' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('tanggal_jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('siswa.kelas_id')
                    ->relationship('siswa.kelas', 'nama')
                    ->label('Filter by Kelas'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'belum_bayar' => 'Belum Bayar',
                        'sebagian' => 'Sebagian',
                    ])
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat Detail')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Tagihan $record): string =>
                        route('filament.admin.resources.tagihans.view', $record)
                    )
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('tanggal_jatuh_tempo', 'asc')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10);
    }
}
