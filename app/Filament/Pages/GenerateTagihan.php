<?php

namespace App\Filament\Pages;

use Filament\Forms;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tagihan;
use Filament\Pages\Page;
use App\Models\TahunAkademik;
use App\Models\JenisPembayaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;

class GenerateTagihan extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Manajemen Tagihan';

    protected static string $view = 'filament.pages.generate-tagihan';

    public array $data = [];

protected function getFormStatePath(): string
{
    return 'data';
}



    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('kelas')
                ->label('Kelas')
                ->options(Kelas::all()->pluck('nama', 'id'))
                ->required(),
                Select::make('jenis_pembayaran_id')
    ->label('Jenis Pembayaran')
    ->options(JenisPembayaran::all()->pluck('nama_pembayaran', 'id'))
    ->required()
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set, callable $get) {
        $jenis = JenisPembayaran::find($state);

        $set('jumlah', $jenis?->nominal ?? 0);

        if ($jenis?->tipe === 'sekali') {
            $set('bulan', null);
        }
    }),

            Select::make('bulan')
            ->required()
            ->options([
                'Januari' => 'Januari',
                'Februari' => 'Februari',
                'Maret' => 'Maret',
                'April' => 'April',
                'Mei' => 'Mei',
                'Juni' => 'Juni',
                'Juli' => 'Juli',
                'Agustus' => 'Agustus',
                'September' => 'September',
                'Oktober' => 'Oktober',
                'November' => 'November',
                'Desember' => 'Desember',
            ])
            ->native(false)
            ->visible(function (callable $get) {
                $jenis = JenisPembayaran::find($get('jenis_pembayaran_id'));
                return $jenis?->tipe === 'bulanan';
            }),
            DatePicker::make('tanggal_jatuh_tempo')
                ->label('Tanggal Jatuh Tempo')
                ->date()
        ];
    }

    protected function getFormActions(): array
    {
        return [
        Forms\Components\Actions\Action::make('Generate')
            ->label('Generate Tagihan')
            ->requiresConfirmation() // ⬅️ ini buat munculin konfirmasi Ya/Tidak
            ->modalHeading('Yakin ingin generate tagihan?')
            ->modalDescription('Tagihan akan dibuat untuk semua siswa di kelas yang dipilih.')
            ->modalButton('Ya, Generate')
            ->cancelButtonText('Batal')
            ->submit('generate')
            ->after(function () {
                // Ini optional: munculin notifikasi dari Filament UI
                $this->notify('success', 'Tagihan berhasil digenerate!');
            })
        ];
    }

   public function generate()
{
    $data = $this->form->getState();

    try {
        DB::beginTransaction();

        $siswaList = Siswa::where('kelas_id', $data['kelas'])->get();
        $tahunAkademikId = Kelas::findOrFail($data['kelas'])->tahun?->id;
        $jenisPembayaran = JenisPembayaran::findOrFail($data['jenis_pembayaran_id']);
        $tanggalJatuhTempo = $data['tanggal_jatuh_tempo'] ?? now()->addDays(30);

        foreach ($siswaList as $siswa) {
            Tagihan::create([
                'siswa_id' => $siswa->id,
                'tahun_akademik_id' => $tahunAkademikId,
                'jenis_pembayaran_id' => $jenisPembayaran->id,
                'jumlah' => $jenisPembayaran->nominal,
                'status' => 'belum_bayar',
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
            ]);
        }

        DB::commit();
        $this->form->fill([]);

        Notification::make()
            ->title('Berhasil!')
            ->body('Tagihan berhasil digenerate untuk semua siswa.')
            ->success()
            ->send();
        return redirect()->to('/admin/tagihans');

    } catch (\Throwable $e) {
        DB::rollBack();

        Notification::make()
            ->title('Gagal!')
            ->body('Terjadi kesalahan saat generate tagihan: ' . $e->getMessage())
            ->danger()
            ->send();
    }
}
}
