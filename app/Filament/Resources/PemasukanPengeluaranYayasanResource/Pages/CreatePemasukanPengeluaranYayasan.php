<?php

namespace App\Filament\Resources\PemasukanPengeluaranYayasanResource\Pages;

use App\Filament\Resources\PemasukanPengeluaranYayasanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePemasukanPengeluaranYayasan extends CreateRecord
{
    protected static string $resource = PemasukanPengeluaranYayasanResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
