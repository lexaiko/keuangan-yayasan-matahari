<?php

namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use App\Models\Tagihan;
use App\Models\DetailPembayaran;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EditPembayaran extends EditRecord
{
    protected static string $resource = PembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    // ✅ PERBAIKI: Collect affected tagihan IDs BEFORE delete
                    $affectedTagihanIds = DetailPembayaran::where('pembayaran_id', $this->record->id)
                        ->pluck('tagihan_id')
                        ->unique();
                    
                    // Hapus detail pembayaran
                    DetailPembayaran::where('pembayaran_id', $this->record->id)->delete();
                    
                    // ✅ PERBAIKI: Update status SETELAH delete dengan logic yang benar
                    foreach ($affectedTagihanIds as $tagihanId) {
                        $tagihan = Tagihan::find($tagihanId);
                        if ($tagihan) {
                            // Hitung ulang total dibayar SETELAH penghapusan
                            $totalDibayarSekarang = $tagihan->detailPembayarans()->sum('jumlah_bayar');
                            
                            if ($totalDibayarSekarang == 0) {
                                // Tidak ada pembayaran sama sekali
                                $tagihan->update(['status' => Tagihan::STATUS_BELUM_BAYAR]);
                            } elseif ($totalDibayarSekarang >= $tagihan->jumlah) {
                                // Masih lunas
                                $tagihan->update(['status' => Tagihan::STATUS_LUNAS]);
                            } else {
                                // Ada pembayaran tapi belum lunas = sebagian
                                $tagihan->update(['status' => Tagihan::STATUS_SEBAGIAN]);
                            }
                        }
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $details = DetailPembayaran::where('pembayaran_id', $this->record->id)
            ->with('tagihan.jenisPembayaran')
            ->get();

        $detailArray = [];
        $tagihanIds = [];

        foreach ($details as $detail) {
            // ✅ PERBAIKI: Tampilkan sisa + current payment agar user tidak bingung
            $totalDibayarLain = $detail->tagihan->detailPembayarans()
                ->where('pembayaran_id', '!=', $this->record->id)
                ->sum('jumlah_bayar');
            
            $sisaTagihan = $detail->tagihan->jumlah - $totalDibayarLain;
            
            // ✅ BUAT INFO TEXT YANG SAMA SEPERTI CREATE
            $totalTagihan = $detail->tagihan->jumlah;
            $totalDibayarSemua = $detail->tagihan->detailPembayarans()->sum('jumlah_bayar');
            
            if ($totalDibayarSemua > $detail->jumlah_bayar) {
                // Ada pembayaran lain, tampilkan info sisa
                $infoText = 'Sisa: Rp ' . number_format($sisaTagihan, 0, ',', '.') . ' dari total Rp ' . number_format($totalTagihan, 0, ',', '.');
            } else {
                // Belum ada pembayaran lain
                $infoText = 'Total: Rp ' . number_format($totalTagihan, 0, ',', '.') . ' (belum dibayar)';
            }
            
            $detailArray[] = [
                'tagihan_id' => $detail->tagihan_id,
                'nama_pembayaran' => $detail->tagihan->jenisPembayaran->nama_pembayaran . ' - ' . ($detail->tagihan->bulan ?? ''),
                'jumlah_tagihan' => $infoText, // ✅ SET INFO TEXT SEPERTI CREATE
                'jumlah_bayar' => $detail->jumlah_bayar,
            ];
            $tagihanIds[] = $detail->tagihan_id;
        }

        $data['detail_pembayarans'] = $detailArray;
        $data['tagihan_ids'] = $tagihanIds;
        $data['total_bayar'] = $this->record->jumlah_bayar;

        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($record, $data) {
            // Validasi sebelum update
            $this->validatePembayaranEdit($data, $record->id);
            
            // ✅ PERBAIKI: Collect ALL affected tagihan IDs (old + new)
            $oldTagihanIds = DetailPembayaran::where('pembayaran_id', $record->id)
                ->pluck('tagihan_id')
                ->toArray();
            
            $newTagihanIds = collect($data['detail_pembayarans'] ?? [])
                ->pluck('tagihan_id')
                ->filter()
                ->toArray();
            
            $allAffectedTagihanIds = array_unique(array_merge($oldTagihanIds, $newTagihanIds));
            
            // Update pembayaran utama
            $record->update([
                'siswa_id' => $data['siswa_id'],
                'tanggal_bayar' => $data['tanggal_bayar'],
                'jumlah_bayar' => $data['total_bayar'],
                'tunai' => $data['tunai'],
                'kembalian' => $data['kembalian'] ?? 0,
                'keterangan' => $data['keterangan'] ?? null,
            ]);

            // ✅ PERBAIKI: Hapus detail pembayaran lama
            DetailPembayaran::where('pembayaran_id', $record->id)->delete();

            // ✅ PERBAIKI: Simpan detail pembayaran baru
            if (!empty($data['detail_pembayarans'])) {
                foreach ($data['detail_pembayarans'] as $detail) {
                    if (!empty($detail['tagihan_id'])) {
                        DetailPembayaran::create([
                            'pembayaran_id' => $record->id,
                            'tagihan_id' => $detail['tagihan_id'],
                            'jumlah_bayar' => $detail['jumlah_bayar'],
                        ]);
                    }
                }
            }

            // ✅ PERBAIKI: Update ALL affected tagihan status ONCE at the end
            foreach ($allAffectedTagihanIds as $tagihanId) {
                $tagihan = Tagihan::find($tagihanId);
                if ($tagihan) {
                    $this->updateTagihanStatusSafe($tagihan);
                }
            }

            return $record;
        });
    }

    private function validatePembayaranEdit(array $data, string $excludePembayaranId): void
    {
        if (!empty($data['detail_pembayarans'])) {
            foreach ($data['detail_pembayarans'] as $detail) {
                if (!empty($detail['tagihan_id'])) {
                    $tagihan = Tagihan::find($detail['tagihan_id']);
                    if ($tagihan) {
                        // ✅ PERBAIKI: Hitung sisa tagihan yang benar
                        $totalDibayarLain = $tagihan->detailPembayarans()
                            ->whereHas('pembayaran', function ($query) use ($excludePembayaranId) {
                                $query->where('id', '!=', $excludePembayaranId);
                            })
                            ->sum('jumlah_bayar');
                        
                        $sisaTagihan = $tagihan->jumlah - $totalDibayarLain;
                        $jumlahBayarBaru = floatval($detail['jumlah_bayar'] ?? 0);
                        
                        // Validasi: tidak boleh bayar lebih dari sisa tagihan
                        if ($jumlahBayarBaru > $sisaTagihan) {
                            throw new \Exception(
                                "Jumlah bayar untuk {$tagihan->jenisPembayaran->nama_pembayaran} " .
                                "({$tagihan->bulan}) tidak boleh lebih dari sisa tagihan Rp " . 
                                number_format($sisaTagihan, 0, ',', '.')
                            );
                        }
                        
                        // Validasi: tidak boleh bayar 0 atau negatif
                        if ($jumlahBayarBaru <= 0) {
                            throw new \Exception("Jumlah bayar harus lebih dari 0");
                        }
                    }
                }
            }
        }
    }

    // ✅ PERBAIKI: Method yang aman untuk update status
    private function updateTagihanStatusSafe(Tagihan $tagihan): void
    {
        // Refresh data tagihan dari database untuk memastikan data terbaru
        $tagihan->refresh();
        
        // Hitung total dibayar dari semua detail pembayaran
        $totalDibayar = $tagihan->detailPembayarans()->sum('jumlah_bayar');
        
        if ($totalDibayar == 0) {
            $tagihan->update(['status' => Tagihan::STATUS_BELUM_BAYAR]);
        } elseif ($totalDibayar >= $tagihan->jumlah) {
            $tagihan->update(['status' => Tagihan::STATUS_LUNAS]);
        } else {
            $tagihan->update(['status' => Tagihan::STATUS_SEBAGIAN]);
        }
    }

    // ✅ LEGACY: Keep untuk backward compatibility
    private function updateTagihanStatus(Tagihan $tagihan, float $totalDibayar): void
    {
        $this->updateTagihanStatusSafe($tagihan);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['tagihan_ids']);
        
        if (!empty($data['detail_pembayarans'])) {
            $total = collect($data['detail_pembayarans'])->sum(function ($detail) {
                return floatval($detail['jumlah_bayar'] ?? 0);
            });
            $data['total_bayar'] = $total;
        }

        if (!isset($data['kembalian'])) {
            $tunai = floatval($data['tunai'] ?? 0);
            $total = floatval($data['total_bayar'] ?? 0);
            $data['kembalian'] = max($tunai - $total, 0);
        }

        return $data;
    }
}