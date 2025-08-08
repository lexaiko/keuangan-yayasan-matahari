<?php

namespace App\Filament\Resources\GajiPegawaiResource\Pages;

use App\Filament\Resources\GajiPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGajiPegawais extends ListRecords
{
    protected static string $resource = GajiPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
