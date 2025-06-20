<?php

namespace App\Filament\Resources\PinjamanUserResource\Pages;

use Exception;
use App\Models\SaldoKoperasi;
use Illuminate\Support\Carbon;
use App\Models\AngsuranPinjaman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PinjamanUserResource;
use Filament\Notifications\Notification;

class CreatePinjamanUser extends CreateRecord
{
    protected static string $resource = PinjamanUserResource::class;
    protected static bool $canCreateAnother = false;

    // redirect setelah create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // auto generate angsuran berdasarkan tenor
   public function mutateFormDataBeforeCreate(array $data): array
    {
        $saldoSaatIni = SaldoKoperasi::getSaldo();

        if ($saldoSaatIni < $data['jumlah_pinjam']) {
            Notification::make()
                ->title('Saldo koperasi tidak mencukupi')
                ->body("Saldo saat ini: Rp " . number_format($saldoSaatIni, 0, ',', '.') . " — tidak cukup untuk meminjam Rp " . number_format($data['jumlah_pinjam'], 0, ',', '.'))
                ->danger()
                ->send();

            // Batalkan pembuatan record
            $this->halt(); // ⛔ stop proses penyimpanan
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $pinjaman = $this->record;

        DB::transaction(function () use ($pinjaman) {
            $angsuranBulanan = round($pinjaman->total_kembali / $pinjaman->tenor_bulan, 2);

            for ($i = 1; $i <= $pinjaman->tenor_bulan; $i++) {
                AngsuranPinjaman::create([
                    'id_pinjaman' => $pinjaman->id_pinjaman,
                    'angsuran_ke' => $i,
                    'tanggal_jatuh_tempo' => Carbon::parse($pinjaman->tanggal_pinjam)->addMonths($i),
                    'jumlah_bayar' => $angsuranBulanan,
                    'status' => 'belum',
                ]);
            }

            SaldoKoperasi::create([
                'pelaku_terkait_id' => $pinjaman->user_id,
                'kategori' => 'pinjaman',
                'tanggal' => now(),
                'tipe' => 'keluar',
                'jumlah' => $pinjaman->jumlah_pinjam,
                'keterangan' => 'Peminjaman oleh ' . $pinjaman->user->name,
            ]);
        });

        Notification::make()
            ->title('Pinjaman berhasil dibuat')
            ->success()
            ->send();
    }
}
