<?php

namespace App\Filament\Resources\KategoriPemasukanPengeluaranResource\Pages;

use App\Filament\Resources\KategoriPemasukanPengeluaranResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKategoriPemasukanPengeluaran extends CreateRecord
{
    protected static string $resource = KategoriPemasukanPengeluaranResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
