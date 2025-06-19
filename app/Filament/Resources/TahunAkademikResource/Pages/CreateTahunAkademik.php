<?php

namespace App\Filament\Resources\TahunAkademikResource\Pages;

use App\Filament\Resources\TahunAkademikResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTahunAkademik extends CreateRecord
{
    protected static string $resource = TahunAkademikResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
