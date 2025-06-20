<?php

namespace App\Filament\Resources\PinjamanUserResource\Pages;

use App\Filament\Resources\PinjamanUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPinjamanUsers extends ListRecords
{
    protected static string $resource = PinjamanUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
