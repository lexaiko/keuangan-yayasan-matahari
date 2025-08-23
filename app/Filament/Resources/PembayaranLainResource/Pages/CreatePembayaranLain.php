<?php

namespace App\Filament\Resources\PembayaranLainResource\Pages;

use App\Filament\Resources\PembayaranLainResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePembayaranLain extends CreateRecord
{
    protected static string $resource = PembayaranLainResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
