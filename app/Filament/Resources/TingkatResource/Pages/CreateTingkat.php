<?php

namespace App\Filament\Resources\TingkatResource\Pages;

use App\Filament\Resources\TingkatResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTingkat extends CreateRecord
{
    protected static string $resource = TingkatResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
