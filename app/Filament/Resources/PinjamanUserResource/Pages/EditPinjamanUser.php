<?php

namespace App\Filament\Resources\PinjamanUserResource\Pages;

use App\Filament\Resources\PinjamanUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPinjamanUser extends EditRecord
{

    protected static string $resource = PinjamanUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
    public function getRelations() : array
    {
        return [];
    }

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
