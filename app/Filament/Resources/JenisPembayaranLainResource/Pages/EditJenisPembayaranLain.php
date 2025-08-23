<?php

namespace App\Filament\Resources\JenisPembayaranLainResource\Pages;

use App\Filament\Resources\JenisPembayaranLainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenisPembayaranLain extends EditRecord
{
    protected static string $resource = JenisPembayaranLainResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
