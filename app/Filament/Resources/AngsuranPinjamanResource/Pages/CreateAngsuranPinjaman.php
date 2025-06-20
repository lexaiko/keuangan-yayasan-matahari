<?php

namespace App\Filament\Resources\AngsuranPinjamanResource\Pages;

use App\Filament\Resources\AngsuranPinjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAngsuranPinjaman extends CreateRecord
{
    protected static string $resource = AngsuranPinjamanResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
