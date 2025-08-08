<?php

namespace App\Filament\Resources\TagihanResource\Pages;

use App\Models\Tagihan;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TagihanResource;

class CreateTagihan extends CreateRecord
{
    protected static string $resource = TagihanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Handle repeater data
        $tagihanItems = $data['tagihan_items'] ?? [];

        if (empty($tagihanItems)) {
            // Fallback for single tagihan creation
            return $data;
        }

        // We'll handle multiple creation in handleRecordCreation
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $tagihanItems = $data['tagihan_items'] ?? [];
        $siswaId = $data['siswa_id'];
        $tahunAkademikId = $data['tahun_akademik_id'];

        if (empty($tagihanItems)) {
            // Fallback for single tagihan creation
            return Tagihan::create($data);
        }

        $createdTagihans = [];

        foreach ($tagihanItems as $item) {
            $tagihanData = [
                'siswa_id' => $siswaId,
                'tahun_akademik_id' => $tahunAkademikId,
                'jenis_pembayaran_id' => $item['jenis_pembayaran_id'],
                'bulan' => $item['bulan'] ?? null,
                'jumlah' => $item['jumlah'],
                'tanggal_jatuh_tempo' => $item['tanggal_jatuh_tempo'] ?? null,
                'status' => Tagihan::STATUS_BELUM_BAYAR,
            ];

            $createdTagihans[] = Tagihan::create($tagihanData);
        }

        // Return the first created tagihan for navigation purposes
        return $createdTagihans[0] ?? new Tagihan();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        $count = count($this->data['tagihan_items'] ?? []);

        if ($count > 1) {
            return "Berhasil membuat {$count} tagihan";
        }

        return 'Tagihan berhasil dibuat';
    }
}
