<?php

namespace App\Filament\Resources\TingkatResource\Pages;

use App\Filament\Resources\TingkatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTingkat extends EditRecord
{
    protected static string $resource = TingkatResource::class;

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
