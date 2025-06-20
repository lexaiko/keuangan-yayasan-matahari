<?php

namespace App\Filament\Resources\AngsuranPinjamanResource\Pages;

use App\Filament\Resources\AngsuranPinjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAngsuranPinjaman extends EditRecord
{
    protected static string $resource = AngsuranPinjamanResource::class;

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
