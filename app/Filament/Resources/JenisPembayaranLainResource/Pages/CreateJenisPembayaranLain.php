<?php

namespace App\Filament\Resources\JenisPembayaranLainResource\Pages;

use App\Filament\Resources\JenisPembayaranLainResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJenisPembayaranLain extends CreateRecord
{
    protected static string $resource = JenisPembayaranLainResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
