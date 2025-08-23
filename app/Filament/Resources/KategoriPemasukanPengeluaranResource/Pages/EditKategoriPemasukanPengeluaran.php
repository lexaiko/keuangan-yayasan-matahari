<?php

namespace App\Filament\Resources\KategoriPemasukanPengeluaranResource\Pages;

use App\Filament\Resources\KategoriPemasukanPengeluaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriPemasukanPengeluaran extends EditRecord
{
    protected static string $resource = KategoriPemasukanPengeluaranResource::class;

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
