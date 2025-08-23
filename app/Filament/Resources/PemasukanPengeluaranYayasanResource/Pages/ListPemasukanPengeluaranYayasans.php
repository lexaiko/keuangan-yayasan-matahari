<?php

namespace App\Filament\Resources\PemasukanPengeluaranYayasanResource\Pages;

use App\Filament\Resources\PemasukanPengeluaranYayasanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPemasukanPengeluaranYayasans extends ListRecords
{
    protected static string $resource = PemasukanPengeluaranYayasanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
