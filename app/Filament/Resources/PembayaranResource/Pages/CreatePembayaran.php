<?php

namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use App\Models\Tagihan;
use App\Models\DetailPembayaran;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CreatePembayaran extends CreateRecord
{
    protected static string $resource = PembayaranResource::class;

    // Redirect ke index setelah create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($data) {
            // Buat pembayaran utama
            $pembayaran = parent::handleRecordCreation([
                'siswa_id' => $data['siswa_id'],
                'user_id' => Auth::id(),
                'tanggal_bayar' => $data['tanggal_bayar'],
                'jumlah_bayar' => $data['total_bayar'], // Sesuai dengan form
                'tunai' => $data['tunai'],
                'kembalian' => $data['kembalian'] ?? 0, // Tambahkan kembalian
                'keterangan' => $data['keterangan'] ?? null,
            ]);

            // Simpan detail pembayaran (hanya jika ada)
            if (!empty($data['detail_pembayarans'])) {
                foreach ($data['detail_pembayarans'] as $detail) {
                    // Pastikan tagihan_id ada
                    if (!empty($detail['tagihan_id'])) {
                        DetailPembayaran::create([
                            'pembayaran_id' => $pembayaran->id,
                            'tagihan_id' => $detail['tagihan_id'],
                            'jumlah_bayar' => $detail['jumlah_bayar'],
                        ]);

                        // Update status tagihan
                        $tagihan = Tagihan::find($detail['tagihan_id']);
                        if ($tagihan) {
                            $totalDibayar = $tagihan->detailPembayarans()->sum('jumlah_bayar');

                            if ($totalDibayar >= $tagihan->jumlah) {
                                $tagihan->update(['status' => Tagihan::STATUS_LUNAS]);
                            } else {
                                $tagihan->update(['status' => Tagihan::STATUS_SEBAGIAN]);
                            }
                        }
                    }
                }
            }

            return $pembayaran;
        });
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Cleanup data yang tidak diperlukan sebelum disimpan
        unset($data['tagihan_ids']); // Hapus field helper
        
        // Pastikan total_bayar sesuai dengan sum detail
        if (!empty($data['detail_pembayarans'])) {
            $total = collect($data['detail_pembayarans'])->sum(function ($detail) {
                return floatval($detail['jumlah_bayar'] ?? 0);
            });
            $data['total_bayar'] = $total;
        }

        // Hitung kembalian jika belum ada
        if (!isset($data['kembalian'])) {
            $tunai = floatval($data['tunai'] ?? 0);
            $total = floatval($data['total_bayar'] ?? 0);
            $data['kembalian'] = max($tunai - $total, 0);
        }

        return $data;
    }
}
