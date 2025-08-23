<?php

namespace App\Filament\Resources\JenisPembayaranLainResource\Pages;

use App\Filament\Resources\JenisPembayaranLainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJenisPembayaranLains extends ListRecords
{
    protected static string $resource = JenisPembayaranLainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
