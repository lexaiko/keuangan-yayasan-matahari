<?php

namespace App\Filament\Resources\PinjamanUserResource\Pages;

use App\Filament\Resources\PinjamanUserResource;
use Filament\Resources\Pages\ViewRecord;

use App\Filament\Resources\PinjamanUserResource\RelationManagers\AngsuranRelationManager;

class ViewPinjamanUser extends ViewRecord
{
    protected static string $resource = PinjamanUserResource::class;

    // Hapus tombol Edit/Lainnya jika perlu
    protected function getHeaderActions(): array
    {
        return [];
    }
    public function getRelationManagers(): array
    {
    return [
        AngsuranRelationManager::class,
    ];
}
}
