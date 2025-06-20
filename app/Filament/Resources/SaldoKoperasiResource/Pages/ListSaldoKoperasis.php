<?php

namespace App\Filament\Resources\SaldoKoperasiResource\Pages;

use App\Filament\Resources\SaldoKoperasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSaldoKoperasis extends ListRecords
{
    protected static string $resource = SaldoKoperasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
