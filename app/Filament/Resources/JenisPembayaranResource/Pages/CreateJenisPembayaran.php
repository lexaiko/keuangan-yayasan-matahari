<?php

namespace App\Filament\Resources\JenisPembayaranResource\Pages;

use App\Filament\Resources\JenisPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJenisPembayaran extends CreateRecord
{
    protected static string $resource = JenisPembayaranResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
