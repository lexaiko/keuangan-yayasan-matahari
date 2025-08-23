<?php

namespace App\Filament\Resources\PembayaranLainResource\Pages;

use App\Filament\Resources\PembayaranLainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPembayaranLains extends ListRecords
{
    protected static string $resource = PembayaranLainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
