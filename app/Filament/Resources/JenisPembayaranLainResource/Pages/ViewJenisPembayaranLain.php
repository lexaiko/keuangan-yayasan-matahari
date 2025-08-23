<?php

namespace App\Filament\Resources\JenisPembayaranLainResource\Pages;

use App\Filament\Resources\JenisPembayaranLainResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJenisPembayaranLain extends ViewRecord
{
    protected static string $resource = JenisPembayaranLainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
