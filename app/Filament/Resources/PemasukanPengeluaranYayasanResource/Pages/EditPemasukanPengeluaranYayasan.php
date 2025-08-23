<?php

namespace App\Filament\Resources\PemasukanPengeluaranYayasanResource\Pages;

use App\Filament\Resources\PemasukanPengeluaranYayasanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPemasukanPengeluaranYayasan extends EditRecord
{
    protected static string $resource = PemasukanPengeluaranYayasanResource::class;

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
