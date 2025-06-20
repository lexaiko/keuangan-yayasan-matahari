<?php

namespace App\Filament\Resources\SaldoKoperasiResource\Pages;

use App\Filament\Resources\SaldoKoperasiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSaldoKoperasi extends CreateRecord
{
    protected static string $resource = SaldoKoperasiResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
