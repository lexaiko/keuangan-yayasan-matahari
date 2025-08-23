<?php

namespace App\Filament\Resources\KategoriPemasukanPengeluaranResource\Pages;

use App\Filament\Resources\KategoriPemasukanPengeluaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKategoriPemasukanPengeluaran extends ViewRecord
{
    protected static string $resource = KategoriPemasukanPengeluaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
