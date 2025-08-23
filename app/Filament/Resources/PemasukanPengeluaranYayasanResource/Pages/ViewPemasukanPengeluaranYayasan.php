<?php

namespace App\Filament\Resources\PemasukanPengeluaranYayasanResource\Pages;

use App\Filament\Resources\PemasukanPengeluaranYayasanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPemasukanPengeluaranYayasan extends ViewRecord
{
    protected static string $resource = PemasukanPengeluaranYayasanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
