<?php

namespace App\Filament\Resources\AngsuranPinjamanResource\Pages;

use App\Filament\Resources\AngsuranPinjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAngsuranPinjaman extends ListRecords
{
    protected static string $resource = AngsuranPinjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
