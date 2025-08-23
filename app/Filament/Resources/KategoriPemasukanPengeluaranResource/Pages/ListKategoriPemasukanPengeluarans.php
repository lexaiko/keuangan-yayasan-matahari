<?php

namespace App\Filament\Resources\KategoriPemasukanPengeluaranResource\Pages;

use App\Filament\Resources\KategoriPemasukanPengeluaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriPemasukanPengeluarans extends ListRecords
{
    protected static string $resource = KategoriPemasukanPengeluaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
