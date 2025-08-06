<?php

namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use App\Models\DetailPembayaran;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPembayaran extends ViewRecord
{
    protected static string $resource = PembayaranResource::class;

    // Override untuk memastikan relasi di-load
    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        
        // Load relasi yang diperlukan
        $this->record->load([
            'siswa',
            'detailPembayarans.tagihan.jenisPembayaran'
        ]);

        $this->authorizeAccess();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pembayaran')
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label('ID Pembayaran'),
                        Infolists\Components\TextEntry::make('siswa.nama')
                            ->label('Nama Siswa'),
                        Infolists\Components\TextEntry::make('tanggal_bayar')
                            ->label('Tanggal Bayar')
                            ->date(),
                        Infolists\Components\TextEntry::make('jumlah_bayar')
                            ->label('Total Bayar')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                        Infolists\Components\TextEntry::make('tunai')
                            ->label('Uang Tunai')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                        Infolists\Components\TextEntry::make('kembalian')
                            ->label('Kembalian')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Tidak ada keterangan'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Detail Pembayaran')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('detailPembayarans')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('tagihan.jenisPembayaran.nama_pembayaran')
                                    ->label('Jenis Pembayaran')
                                    ->placeholder('Tidak ada data'),
                                Infolists\Components\TextEntry::make('tagihan.bulan')
                                    ->label('Bulan')
                                    ->placeholder('Tidak ada bulan'),
                                Infolists\Components\TextEntry::make('tagihan.jumlah')
                                    ->label('Jumlah Tagihan')
                                    ->formatStateUsing(fn ($state) => $state ? 'Rp ' . number_format($state, 0, ',', '.') : 'Rp 0'),
                                Infolists\Components\TextEntry::make('jumlah_bayar')
                                    ->label('Jumlah Dibayar')
                                    ->formatStateUsing(fn ($state) => $state ? 'Rp ' . number_format($state, 0, ',', '.') : 'Rp 0'),
                            ])
                            ->columns(4)
                            ->contained(false),
                    ]),
            ]);
    }
}
