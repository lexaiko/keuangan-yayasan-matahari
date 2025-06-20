<?php

namespace App\Filament\Resources\PinjamanUserResource\RelationManagers;

use App\Models\AngsuranPinjaman;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;


class AngsuranRelationManager extends RelationManager
{


    protected static string $relationship = 'angsuran'; // method relasi di model PinjamanUser
    protected static ?string $title = 'Detail Angsuran';
    protected static ?string $recordTitleAttribute = 'angsuran_ke';
    protected static ?string $label = 'Angsuran';


    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('angsuran_ke')->sortable(),
                TextColumn::make('tanggal_jatuh_tempo')->date(),
                TextColumn::make('tanggal_bayar')->date(),
                TextColumn::make('jumlah_bayar')->money('IDR'),
                TextColumn::make('status')->badge()->color(fn ($state) => $state === 'sudah' ? 'success' : ($state === 'terlambat' ? 'danger' : 'warning')),
            ])
            ->actions([
            Action::make('konfirmasiBayar')
                ->label('Konfirmasi Bayar')
                ->visible(fn ($record) => $record->status !== 'sudah')
                ->requiresConfirmation()
                ->color('success')
                ->action(function ($record) {
                    $record->update([
                        'status' => 'sudah',
                        'tanggal_bayar' => now(),
                    ]);

                    // Tambah ke saldo koperasi
                    DB::table('saldo_koperasis')->insert([
                        'pelaku_terkait_id' => $record->pinjaman->user_id,
                        'kategori' => 'pinjaman',
                        'tanggal' => now(),
                        'tipe' => 'masuk',
                        'jumlah' => $record->jumlah_bayar,
                        'keterangan' => 'Pembayaran angsuran ke-' . $record->angsuran_ke,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }),
        ])
            ->paginated()
            ->defaultSort('angsuran_ke');
    }
}
