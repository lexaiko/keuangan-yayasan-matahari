<?php

namespace App\Filament\Resources\SaldoKoperasiResource\Pages;

use App\Filament\Resources\SaldoKoperasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSaldoKoperasi extends EditRecord
{
    protected static string $resource = SaldoKoperasiResource::class;

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
