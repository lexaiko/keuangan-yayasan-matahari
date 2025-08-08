<?php

namespace App\Filament\Resources\GajiPegawaiResource\Pages;

use App\Filament\Resources\GajiPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGajiPegawai extends CreateRecord
{
    protected static string $resource = GajiPegawaiResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
