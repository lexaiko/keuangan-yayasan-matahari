<?php

namespace App\Filament\Resources\AdminResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\SaldoKoperasi;
use Filament\Widgets\TableWidget as BaseWidget;

class SaldoTableWidget extends BaseWidget
{
    protected int|string|array $columnSpan = '1'; // bar chart satu baris penuh


    protected static ?string $heading = 'History Tabungan Pegawai';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                SaldoKoperasi::query()
                    ->select([
                        'id_saldo',
                        'tanggal',
                        'tipe',
                        'jumlah',
                        'keterangan',
                    ])
                    ->where('kategori', 'tabungan')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->money('idr', true)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->sortable()
                    ->searchable(),
            ]);
    }
}
