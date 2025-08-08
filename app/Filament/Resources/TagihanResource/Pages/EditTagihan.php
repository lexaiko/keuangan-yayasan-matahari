<?php

namespace App\Filament\Resources\TagihanResource\Pages;

use App\Filament\Resources\TagihanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTagihan extends EditRecord
{
    protected static string $resource = TagihanResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert single tagihan to repeater format for editing
        $data['tagihan_items'] = [
            [
                'jenis_pembayaran_id' => $data['jenis_pembayaran_id'] ?? null,
                'bulan' => $data['bulan'] ?? null,
                'jumlah' => $data['jumlah'] ?? 0,
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'] ?? null,
            ]
        ];

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Convert repeater data back to single tagihan format
        $firstItem = $data['tagihan_items'][0] ?? [];

        return [
            'siswa_id' => $data['siswa_id'],
            'tahun_akademik_id' => $data['tahun_akademik_id'],
            'jenis_pembayaran_id' => $firstItem['jenis_pembayaran_id'] ?? null,
            'bulan' => $firstItem['bulan'] ?? null,
            'jumlah' => $firstItem['jumlah'] ?? 0,
            'tanggal_jatuh_tempo' => $firstItem['tanggal_jatuh_tempo'] ?? null,
            'status' => $this->record->status, // Keep existing status
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
